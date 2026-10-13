@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            <div class="card-modern shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header card-header-grad text-white text-center py-4">
                    <h3 class="fw-bold mb-0">➕ إضافة مسؤول جديد</h3>
                    <p class="small opacity-75 mt-2">أدخل بيانات المسؤول الجديد</p>
                </div>

                <div class="card-body p-5">
                    <form id="adminCreateForm" action="{{ route('admins.store') }}" method="POST" novalidate>
                        @csrf

                        {{-- الاسم --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark" for="name">الاسم الكامل</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   class="form-control form-control-modern @error('name') is-invalid @enderror"
                                   placeholder="أدخل الاسم الكامل"
                                   required>
                            <div class="field-error text-danger small mt-2" id="nameError">
                                @error('name') {{ $message }} @enderror
                            </div>
                        </div>

                        {{-- البريد --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark" for="email">البريد الإلكتروني</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="form-control form-control-modern @error('email') is-invalid @enderror"
                                   placeholder="example@mail.com"
                                   required>
                            <div class="field-error text-danger small mt-2" id="emailError">
                                @error('email') {{ $message }} @enderror
                            </div>
                        </div>

                        {{-- اختيار النوع --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark" for="adminType">نوع المسؤول</label>
                            <select name="type" id="adminType"
                                    class="form-select form-control-modern @error('type') is-invalid @enderror">
                                <option value="admin" {{ old('type', 'admin') === 'admin' ? 'selected' : '' }}>
                                    👮 مسؤول كامل (وصول شامل)
                                </option>
                                <option value="financier" {{ old('type') === 'financier' ? 'selected' : '' }}>
                                    💰 مالي (حالة مالية)
                                </option>
                                <option value="verify_qr" {{ old('type') === 'verify_qr' ? 'selected' : '' }}>
                                    📷 مدقق QR (تحقق من التذاكر)
                                </option>
                            </select>
                            <div class="text-muted small mt-1">
                                اختر « مالي » لحساب مخصص لحالة الدفع المالية، أو « مدقق QR » لحساب مسح التذاكر عند مدخل المنشأة.
                            </div>
                            <div class="field-error text-danger small mt-2" id="typeError">
                                @error('type') {{ $message }} @enderror
                            </div>
                        </div>

                        {{-- اختيار المجمّع --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark" for="adminComplex">المجمّع الرياضي التابع له</label>
                            <select name="complex_id" id="adminComplex"
                                    class="form-select form-control-modern @error('complex_id') is-invalid @enderror">
                                <option value="">— اختر المجمّع —</option>

                                @foreach($complexes as $complex)
                                    <option value="{{ $complex->id }}"
                                        {{ old('complex_id') == $complex->id ? 'selected' : '' }}>
                                        {{ $complex->nom }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="complexHint" class="text-muted small mt-1">
                                اتركه فارغا للمسؤول الكامل، أو اختر مجمعا للمسؤول المختص.
                            </div>
                            <div class="field-error text-danger small mt-2" id="complexError">
                                @error('complex_id') {{ $message }} @enderror
                            </div>
                        </div>

                        {{-- كلمة المرور --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark" for="password">كلمة المرور</label>
                            <div class="input-group">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-control form-control-modern password-input @error('password') is-invalid @enderror"
                                       placeholder="كلمة مرور قوية (6 أحرف على الأقل)"
                                       required>
                                <button type="button" class="btn btn-outline-secondary toggle-password">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div class="field-error text-danger small mt-2" id="passwordError">
                                @error('password') {{ $message }} @enderror
                            </div>
                        </div>

                        {{-- تأكيد كلمة المرور --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold text-dark" for="password_confirmation">تأكيد كلمة المرور</label>
                            <div class="input-group">
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="form-control form-control-modern password-input"
                                       placeholder="أعد كتابة كلمة المرور"
                                       required>
                                <button type="button" class="btn btn-outline-secondary toggle-password">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            <div class="field-error text-danger small mt-2" id="password_confirmationError"></div>
                        </div>

                        {{-- أزرار --}}
                        <div class="d-flex flex-wrap gap-3 justify-content-end">
                            <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4 shadow-sm">
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary btn-glow btn-lg rounded-pill px-4">
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



{{-- ======================== STYLES MODERN CLAIR RESPONSIVE ======================== --}}
<style>
    :root {
        --primary: #4361ee;
        --primary-2: #4cc9f0;
        --primary-gradient: linear-gradient(135deg, #4361ee, #4cc9f0);
    }

    body {
        background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .card-modern {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .card-header-grad {
        background: var(--primary-gradient);
    }

    .form-control-modern {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #e2e8f0;
        border-radius: 0.9rem;
        padding: 0.85rem 1.15rem;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
    }

    .form-control-modern::placeholder {
        color: #94a3b8;
    }

    .form-control-modern:focus {
        background: white;
        transform: translateY(-2px);
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.18);
    }

    /* --- États de validation instantanée --- */
    .form-control-modern.is-invalid {
        border-color: #e3342f !important;
        background: #fff5f5 !important;
        box-shadow: 0 0 0 4px rgba(227, 52, 47, 0.12) !important;
    }

    .form-control-modern.is-valid {
        border-color: #38a169 !important;
        background: #f0fff4 !important;
        box-shadow: 0 0 0 4px rgba(56, 161, 105, 0.12) !important;
    }

    .form-control-modern.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(227, 52, 47, 0.22) !important;
    }

    .form-control-modern.is-valid:focus {
        box-shadow: 0 0 0 4px rgba(56, 161, 105, 0.22) !important;
    }

    .input-group .form-control-modern {
        border-radius: 0 0.9rem 0.9rem 0;
    }

    .input-group .toggle-password {
        border-radius: 0.9rem 0 0 0.9rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: var(--primary);
    }

    .toggle-password:hover {
        background: rgba(67, 97, 238, 0.08);
    }

    .btn-glow {
        background: var(--primary-gradient);
        color: white;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 6px 18px rgba(67, 97, 238, 0.35);
    }

    .btn-glow:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(67, 97, 238, 0.45);
        color: white;
    }

    .field-error {
        min-height: 0;
        display: none;
    }

    .field-error.show {
        display: block;
    }

    @media (max-width: 576px) {
        .card-body { padding: 1.75rem; }
        .btn-lg { font-size: 0.95rem; padding-left: 1.5rem; padding-right: 1.5rem; }
    }
</style>


{{-- ======================== SCRIPT SHOW/HIDE PASSWORD + VALIDATION ======================== --}}
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

    const form      = document.getElementById('adminCreateForm');
    const nameEl    = document.getElementById('name');
    const emailEl   = document.getElementById('email');
    const typeSel   = document.getElementById('adminType');
    const complexSel= document.getElementById('adminComplex');
    const passEl    = document.getElementById('password');
    const passConfEl= document.getElementById('password_confirmation');
    const complexHint = document.getElementById('complexHint');

    function setError(el, msg) {
        if (!el) return;
        const err = el.closest('.mb-4, .mb-5').querySelector('.field-error');
        if (err) {
            if (msg) {
                err.textContent = msg;
                err.classList.add('show');
            } else {
                err.textContent = '';
                err.classList.remove('show');
            }
        }
        if (msg) {
            el.classList.remove('is-valid');
            el.classList.add('is-invalid');
        } else {
            el.classList.remove('is-invalid');
            if (el.value && el.closest('.mb-4, .mb-5')) {
                el.classList.add('is-valid');
            } else {
                el.classList.remove('is-valid');
            }
        }
    }

    function validateName() {
        const v = nameEl.value.trim();
        if (!v) return setError(nameEl, 'الاسم الكامل مطلوب.');
        if (v.length < 3) return setError(nameEl, 'الاسم قصير جدا (3 أحرف على الأقل).');
        return setError(nameEl, '');
    }

    function validateEmail() {
        const v = emailEl.value.trim();
        if (!v) return setError(emailEl, 'البريد الإلكتروني مطلوب.');
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!re.test(v)) return setError(emailEl, 'صيغة البريد الإلكتروني غير صحيحة.');
        return setError(emailEl, '');
    }

    function validatePassword() {
        const v = passEl.value;
        if (!v) return setError(passEl, 'كلمة المرور مطلوبة.');
        if (v.length < 6) return setError(passEl, 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.');
        if (passConfEl.value && passConfEl.value !== v) {
            setError(passConfEl, 'كلمتا المرور غير متطابقتين.');
        } else {
            validatePasswordConfirmation();
        }
        return setError(passEl, '');
    }

    function validatePasswordConfirmation() {
        const v = passConfEl.value;
        if (!v) return setError(passConfEl, 'تأكيد كلمة المرور مطلوب.');
        if (v !== passEl.value) return setError(passConfEl, 'كلمتا المرور غير متطابقتين.');
        return setError(passConfEl, '');
    }

    function validateComplex() {
        if (typeSel.value === 'verify_qr') {
            if (!complexSel.value) {
                return setError(complexSel, 'إلزامي: اختر المجمّع لمدقق QR.');
            }
        }
        return setError(complexSel, '');
    }

    function refreshComplex() {
        if (typeSel && complexSel && complexHint) {
            const isVerify = typeSel.value === 'verify_qr';
            if (isVerify) {
                complexHint.textContent = '⚠️ إلزامي: يجب اختيار مجمع لمدقق QR.';
                complexHint.style.color = '#d53f8c';
                complexSel.setAttribute('required', 'required');
            } else {
                complexHint.textContent = 'اتركه فارغا للمسؤول الكامل، أو اختر مجمعا للمسؤول المختص.';
                complexHint.style.color = '';
                complexSel.removeAttribute('required');
            }
        }
        validateComplex();
    }

    if (nameEl)    nameEl.addEventListener('input', validateName);
    if (emailEl)   emailEl.addEventListener('input', validateEmail);
    if (passEl)    passEl.addEventListener('input', function () { validatePassword(); });
    if (passConfEl) passConfEl.addEventListener('input', validatePasswordConfirmation);
    if (typeSel)   { typeSel.addEventListener('change', refreshComplex); refreshComplex(); }
    if (complexSel) complexSel.addEventListener('change', validateComplex);

    if (form) {
        form.addEventListener('submit', function (e) {
            validateName();
            validateEmail();
            validatePassword();
            validatePasswordConfirmation();
            refreshComplex();

            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                e.preventDefault();
                firstInvalid.focus();
            }
        });
    }
});
</script>
