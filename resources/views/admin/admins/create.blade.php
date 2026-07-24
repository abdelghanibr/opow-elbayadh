@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            <div class="card-modern shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-dark text-white text-center py-4">
                    <h3 class="fw-bold mb-0">➕ إضافة مسؤول جديد</h3>
                    <p class="small opacity-75 mt-2">أدخل بيانات المسؤول الجديد</p>
                </div>

                <div class="card-body p-5">
                    <form action="{{ route('admins.store') }}" method="POST" novalidate>
                        @csrf

                        {{-- الاسم --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-light">الاسم الكامل</label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   class="form-control form-control-modern @error('name') is-invalid @enderror"
                                   placeholder="أدخل الاسم الكامل"
                                   required>
                            @error('name')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- البريد --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-light">البريد الإلكتروني</label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="form-control form-control-modern @error('email') is-invalid @enderror"
                                   placeholder="example@mail.com"
                                   required>
                            @error('email')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- اختيار المجمّع --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-light">المجمّع الرياضي التابع له</label>
                            <select name="complex_id"
                                    class="form-select form-control-modern @error('complex_id') is-invalid @enderror">
                                <option value="">— اختر المجمّع —</option>

                                @foreach($complexes as $complex)
                                    <option value="{{ $complex->id }}"
                                        {{ old('complex_id') == $complex->id ? 'selected' : '' }}>
                                        {{ $complex->nom }}
                                    </option>
                                @endforeach
                            </select>

                            @error('complex_id')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- كلمة المرور --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-light">كلمة المرور</label>
                            <div class="input-group">
                                <input type="password"
                                       name="password"
                                       class="form-control form-control-modern password-input @error('password') is-invalid @enderror"
                                       placeholder="كلمة مرور قوية"
                                       required>
                                <button type="button" class="btn btn-outline-light toggle-password">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- تأكيد كلمة المرور --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold text-light">تأكيد كلمة المرور</label>
                            <div class="input-group">
                                <input type="password"
                                       name="password_confirmation"
                                       class="form-control form-control-modern password-input"
                                       placeholder="أعد كتابة كلمة المرور"
                                       required>
                                <button type="button" class="btn btn-outline-light toggle-password">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- أزرار --}}
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('admins.index') }}" class="btn btn-outline-light btn-lg rounded-pill px-5 shadow-sm">
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-dark-glow btn-lg rounded-pill px-5 shadow-lg">
                                💾 حفظ المسؤول
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection



{{-- ======================== DARK STYLES ======================== --}}
<style>
    :root {
        --dark-bg: #0f172a;
        --dark-card: rgba(30, 41, 59, 0.75);
        --primary-gradient: linear-gradient(135deg, #1e293b, #64748b);
        --shadow-dark: 0 10px 30px rgba(0,0,0,0.4);
    }

    body {
        background: var(--dark-bg);
        color: #e2e8f0;
    }

    .card-modern {
        background: var(--dark-card);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: var(--shadow-dark);
    }

    .bg-gradient-dark {
        background: var(--primary-gradient);
    }

    .form-control-modern {
        background: rgba(255,255,255,0.07);
        border: none;
        border-radius: 1rem;
        padding: 0.9rem 1.2rem;
        color: #e2e8f0;
    }

    .form-control-modern:focus {
        background: rgba(0,0,0,0.3);
        box-shadow: 0 0 0 3px rgba(255,255,255,0.2);
        color: #fff;
    }

    .btn-dark-glow {
        background: #1e293b;
        color: white;
        transition: 0.3s;
        border: 1px solid #334155;
    }

    .btn-dark-glow:hover {
        background: #334155;
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.5);
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
