@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            <div class="card-modern shadow-lg border-0 rounded-4 overflow-hidden">

                <div class="card-header bg-gradient-primary text-white text-center py-4">
                    <h3 class="fw-bold mb-0">✏ تعديل بيانات المسؤول</h3>
                    <p class="small opacity-75 mt-2">تحديث بيانات {{ $admin->name }}</p>
                </div>

                <div class="card-body p-5">
                    <form action="{{ route('admins.update', $admin->id) }}" method="POST" novalidate>
                        @csrf
                       

                        {{-- الاسم --}}
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold text-primary">الاسم الكامل</label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $admin->name) }}"
                                   class="form-control form-control-modern @error('name') is-invalid @enderror"
                                   placeholder="أدخل الاسم الكامل"
                                   required>
                            @error('name')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- البريد الإلكتروني --}}
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold text-primary">البريد الإلكتروني</label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $admin->email) }}"
                                   class="form-control form-control-modern @error('email') is-invalid @enderror"
                                   placeholder="example@mail.com"
                                   required>
                            @error('email')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- اختيار المجمع الرياضي --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-primary">المجمّع الرياضي التابع له</label>
                            <select name="complex_id"
                                    class="form-select form-control-modern @error('complex_id') is-invalid @enderror">
                                <option value="">— اختر المجمّع —</option>

                                @foreach($complexes as $complex)
                                    <option value="{{ $complex->id }}"
                                        {{ old('complex_id', $admin->complex_id) == $complex->id ? 'selected' : '' }}>
                                        {{ $complex->nom }}
                                    </option>
                                @endforeach
                            </select>

                            @error('complex_id')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- كلمة المرور الجديدة --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-primary">
                                كلمة المرور الجديدة <span class="text-muted small">(اختياري)</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       name="password"
                                       class="form-control form-control-modern password-input @error('password') is-invalid @enderror"
                                       placeholder="اتركه فارغا لعدم التغيير">
                                <button type="button" class="btn btn-outline-primary toggle-password">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- تأكيد كلمة المرور --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold text-primary">تأكيد كلمة المرور الجديدة</label>
                            <div class="input-group">
                                <input type="password"
                                       name="password_confirmation"
                                       class="form-control form-control-modern password-input"
                                       placeholder="أعد كتابة كلمة المرور">
                                <button type="button" class="btn btn-outline-primary toggle-password">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- أزرار --}}
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('admins.index') }}"
                               class="btn btn-outline-secondary btn-lg rounded-pill px-5 shadow-sm">
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg btn-glow">
                                💾 حفظ التغييرات
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
{{-- ======================== STYLES 2026 ======================== --}}
<style>
    :root {
        --primary: #4361ee;
        --primary-gradient: linear-gradient(135deg, #4361ee, #4cc9f0);
        --glass: rgba(255, 255, 255, 0.2);
        --shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        --border-glass: 1px solid rgba(255, 255, 255, 0.3);
    }

    body {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .card-modern {
        background: var(--glass);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: var(--border-glass);
        box-shadow: var(--shadow);
    }

    .bg-gradient-primary {
        background: var(--primary-gradient);
    }

    .form-control-modern {
        background: rgba(255, 255, 255, 0.7);
        border: none;
        border-radius: 1rem;
        padding: 0.9rem 1.2rem;
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .form-control-modern:focus {
        background: white;
        transform: translateY(-3px);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.2);
    }

    .input-group .form-control-modern {
        border-radius: 1rem 0 0 1rem;
    }

    .input-group .btn {
        border-radius: 0 1rem 1rem 0;
        border: none;
        background: rgba(255, 255, 255, 0.7);
    }

    .toggle-password:hover {
        background: rgba(67, 97, 238, 0.1);
    }

    .btn-glow {
        background: var(--primary-gradient);
        color: white;
        transition: all 0.4s ease;
    }

    .btn-glow:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(67, 97, 238, 0.4);
        color: white;
    }

    .bi-eye-slash::before { content: "\f33e"; }
    .bi-eye::before { content: "\f340"; }

    @media (max-width: 576px) {
        .card-body { padding: 2rem; }
    }
</style>
{{-- ======================== SCRIPT SHOW/HIDE PASSWORD ======================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-password').forEach(button => {

        button.addEventListener('click', function () {

            const input = this.closest('.input-group').querySelector('.password-input');
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    });
});
</script>
