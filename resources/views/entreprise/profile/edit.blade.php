@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; text-align:right; max-width:900px;">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-bold text-success mb-0">
            🏢 تعديل بيانات المؤسسة
        </h3>
    </div>

    {{-- Success --}}
    @if(session('success'))
        <div class="alert alert-success text-center fw-bold shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Card --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4 p-md-5">

            <form action="{{ route('entreprise.profile.update') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- ================= بيانات المؤسسة ================= --}}
                <h5 class="fw-bold mb-3 text-secondary">
                    📋 المعلومات الأساسية
                </h5>

                <div class="row g-3">

                    {{-- اسم المؤسسة --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">اسم المؤسسة</label>
                        <input type="text"
                               name="name"
                               class="form-control form-control-lg"
                               value="{{ $user->name }}"
                               required>
                    </div>

                    {{-- البريد الإلكتروني --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">البريد الإلكتروني</label>
                        <input type="email"
                               name="email"
                               class="form-control form-control-lg"
                               value="{{ $user->email }}"
                               required>
                    </div>

                    {{-- الهاتف --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">رقم الهاتف</label>
                        <input type="text"
                               name="phone"
                               class="form-control form-control-lg"
                               value="{{ $user->phone }}">
                    </div>

                    {{-- العنوان (اختياري إن وجد عندك) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">عنوان المؤسسة</label>
                        <input type="text"
                               name="address"
                               class="form-control form-control-lg"
                               value="{{ $user->address ?? '' }}">
                    </div>

                </div>

                {{-- ================= كلمة المرور ================= --}}
                <hr class="my-5">

                <h5 class="fw-bold mb-3 text-secondary">
                    🔐 تغيير كلمة المرور
                    <small class="text-muted">(اختياري)</small>
                </h5>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-bold">كلمة المرور الجديدة</label>
                        <div class="input-group input-group-lg">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control"
                                   placeholder="اتركها فارغة إذا لا تريد التغيير">
                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="togglePassword('password')">
                                👁
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">تأكيد كلمة المرور</label>
                        <div class="input-group input-group-lg">
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="form-control"
                                   placeholder="إعادة كتابة كلمة المرور">
                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="togglePassword('password_confirmation')">
                                👁
                            </button>
                        </div>
                    </div>

                </div>

                {{-- ================= Actions ================= --}}
                <div class="d-flex justify-content-between align-items-center mt-5">
                    <a href="{{ url()->previous() }}"
                       class="btn btn-outline-secondary btn-lg px-4">
                        ⬅ رجوع
                    </a>

                    <button class="btn btn-success btn-lg px-5 fw-bold shadow">
                        💾 تحديث البيانات
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- JS --}}
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
