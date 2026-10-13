@extends('layouts.app')

@section('title', 'تسجيل مؤسسة / شركة جديدة')

@section('content')

<div class="enterprise-register-bg">

    <div class="enterprise-register-box">

        <!-- Header -->
        <div class="text-center mb-3">
            <img src="{{ asset('images/djs-logo.png') }}" width="90">
            <h3 class="fw-bold mt-2">تسجيل مؤسسة / شركة</h3>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('entreprise.register.post') }}" id="companyRegisterForm" enctype="multipart/form-data" novalidate>
            @csrf
              @if($selectedComplex)
    <div class="alert alert-info text-center fw-bold">
        التسجيل في المركب: <span class="text-primary">{{ $selectedComplex->nom }}</span>
        <br>
        <small>رقم المركب: {{ $selectedComplex->id }}</small>
    </div>
@endif
            <div class="row g-4">

                <!-- Company Name -->
                <div class="col-md-6">
                    <label class="form-label">اسم الشركة</label>
                    <input type="text" name="name" id="company_name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" required>

                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Company Type -->
                <div class="col-md-6">
                    <label class="form-label">نوع المؤسسة</label>
                    <select name="company_type" id="company_type"
                        class="form-select @error('company_type') is-invalid @enderror" required>
                        <option value="">-- اختر النوع --</option>
                        <option value="public" {{ old('company_type') == 'public' ? 'selected' : '' }}>مؤسسة عمومية</option>
                        <option value="privee" {{ old('company_type') == 'privee' ? 'selected' : '' }}>مؤسسة خاصة</option>
                    </select>

                    @error('company_type')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" id="company_email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" required>

                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Complex -->

<input type="hidden" name="complex_id" value="{{ $complexId }}">

                <!-- Phone: responsable de l'inscription -->
                <div class="col-md-6">
                    <label class="form-label">رقم هاتف المسؤول عن التسجيل</label>
                    <input type="tel" name="phone" id="company_phone" value="{{ old('phone') }}"
                        class="form-control @error('phone') is-invalid @enderror"
                        placeholder="0550 12 34 56" inputmode="tel" autocomplete="tel" required>

                    @error('phone')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- تم إخفاء رقم السجل التجاري ووثائق الشركة (غير إلزامية) --}}

                <!-- Password -->
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" id="company_password"
                        class="form-control @error('password') is-invalid @enderror" required>

                    <small class="text-muted d-block mt-1" style="line-height:1.7;">
                        8 أحرف على الأقل، وتتضمن: حرفاً كبيراً (A-Z)، حرفاً صغيراً (a-z)، رقماً (0-9)، ورمزاً خاصاً (@ # $ ...).
                    </small>

                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" id="company_password_confirmation" class="form-control" required>
                </div>

                <!-- Not robot (reCAPTCHA) -->
@if (!app()->environment('local'))
<div class="mt-3">
    <label class="fw-bold d-block mb-1">🔐 التحقق أنني لست روبوت :</label>

    <div class="captcha-wrapper">
        <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
    </div>

    @error('g-recaptcha-response')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>
@endif

                <!-- Privacy Zone -->
                <div class="privacy-zone-enterprise mt-4">

                    <div class="privacy-header">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>حماية المعطيات الشخصية</span>
                    </div>

                    <div class="privacy-content">

                        <p>
                            أوافق على جمع ومعالجة بياناتي طبقًا للقانون الجزائري رقم 18-07
                            المتعلق بحماية الأشخاص في مجال معالجة البيانات ذات الطابع الشخصي.
                        </p>

                        <div class="form-check mt-2">
                            <input class="form-check-input @error('privacy_policy') is-invalid @enderror"
                                type="checkbox" name="privacy_policy" id="company_privacy" value="1" required>

                            <label class="form-check-label fw-bold">
                                أوافق على <span class="privacy-link">سياسة حماية البيانات</span>
                            </label>

                            @error('privacy_policy')
                                <div class="invalid-feedback d-block">
                                    يجب الموافقة على السياسة
                                </div>
                            @enderror
                        </div>

                    </div>

                </div>

                <!-- Submit -->
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-success w-100 py-2">إنشاء الحساب</button>
                </div>

            </div>

            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    {{ $errors->first() }}
                </div>
            @endif

        </form>

        <p class="text-center mt-3">
            لديك حساب؟ <a href="{{ route('entreprise.login') }}">تسجيل الدخول</a>
        </p>

    </div>

</div>

{{-- Inline CSS --}}
<style>

.enterprise-register-bg {
    background:#e8f5e9;
    padding:30px;
}

.enterprise-register-box {
    background:white;
    width:95%;
    max-width:900px;
    margin:auto;
    padding:40px;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.12);
}

/* PRIVACY ZONE */
.privacy-zone-enterprise {
    background: linear-gradient(135deg, #0a4f88, #2563eb);
    border-radius:18px;
    padding:18px 20px;
    color:#ffffff;
    box-shadow:0 12px 30px rgba(0,0,0,.15);
}

.privacy-header {
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:900;
    font-size:15px;
    margin-bottom:10px;
}

.privacy-header i {
    font-size:20px;
    color:#e0f2fe;
}

.privacy-content {
    background:rgba(255,255,255,0.12);
    border-radius:14px;
    padding:14px 16px;
    font-size:14px;
    line-height:1.8;
}

.privacy-content p {
    margin-bottom:10px;
    color:#f0f9ff;
}

.privacy-link {
    color:#a7f3d0;
    font-weight:900;
    text-decoration:underline;
}

.privacy-link:hover {
    color:white;
}

.captcha-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 6px;
}

</style>

@push('js')
@if (!app()->environment('local'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
<script>
var checkEmailUrl = "{{ route('auth.check-email') }}";
var emailExists = false;
var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function setFieldError(input, msg) {
    if (!input) return true;
    var err = input.parentNode.querySelector('.js-field-error');
    if (msg) {
        if (!err) {
            err = document.createElement('div');
            err.className = 'text-danger small mt-1 js-field-error';
            input.parentNode.appendChild(err);
        }
        err.textContent = msg;
        input.classList.add('is-invalid');
        return false;
    }
    if (err) err.remove();
    input.classList.remove('is-invalid');
    return true;
}

var validators = {
    company_name: function() {
        var v = document.getElementById('company_name').value.trim();
        if (!v) return 'اسم الشركة مطلوب.';
        if (v.length < 3) return 'اسم الشركة قصير جداً (3 أحرف على الأقل).';
        return null;
    },
    company_email: function() {
        var v = document.getElementById('company_email').value.trim();
        if (!v) return 'البريد الإلكتروني مطلوب.';
        if (!emailRe.test(v)) return 'أدخل بريد إلكتروني صحيح.';
        if (emailExists) return 'هذا البريد الإلكتروني مسجل بالفعل.';
        return null;
    },
    company_phone: function() {
        var raw = document.getElementById('company_phone').value.trim();
        if (!raw) return 'رقم هاتف المسؤول مطلوب.';
        var v = raw.replace(/[\s.\-()]/g, '');
        if (!/^0[0-9]{8,9}$/.test(v)) return 'أدخل رقم هاتف جزائري صحيح (مثال: 0550 12 34 56).';
        return null;
    },
    company_type: function() {
        var v = document.getElementById('company_type').value;
        return v ? null : 'يرجى اختيار نوع المؤسسة.';
    },
    company_password: function() {
        var v = document.getElementById('company_password').value;
        if (!v) return 'كلمة المرور مطلوبة.';
        if (v.length < 8) return 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.';
        if (!/[a-z]/.test(v) || !/[A-Z]/.test(v) || !/[0-9]/.test(v) || !/[^a-zA-Z0-9]/.test(v))
            return 'كلمة المرور يجب أن تحتوي على حرف كبير وحرف صغير ورقم ورمز خاص.';
        return null;
    },
    company_password_confirmation: function() {
        var p = document.getElementById('company_password').value;
        var v = document.getElementById('company_password_confirmation').value;
        if (!v) return 'يرجى تأكيد كلمة المرور.';
        if (p !== v) return 'كلمتا المرور غير متطابقتين.';
        return null;
    },
    company_privacy: function() {
        return document.getElementById('company_privacy').checked ? null : 'يجب الموافقة على سياسة حماية البيانات.';
    }
};

function validateField(id) {
    var input = document.getElementById(id);
    if (!input || !validators[id]) return true;
    return setFieldError(input, validators[id]());
}

Object.keys(validators).forEach(function(id) {
    var input = document.getElementById(id);
    if (!input) return;

    input.addEventListener('blur', function() { validateField(id); });
    input.addEventListener('change', function() { validateField(id); });
    input.addEventListener('input', function() { validateField(id); });
});

// كلمة المرور: إعادة التحقق من التأكيد مباشرة
document.getElementById('company_password').addEventListener('input', function() {
    var confirmInput = document.getElementById('company_password_confirmation');
    if (confirmInput.value || confirmInput.classList.contains('is-invalid')) {
        validateField('company_password_confirmation');
    }
});

// البريد: فحص البريد المكرر عبر AJAX
document.getElementById('company_email').addEventListener('blur', function() {
    var email = this.value.trim();
    if (!email || !emailRe.test(email)) return;
    fetch(checkEmailUrl + '?email=' + encodeURIComponent(email))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            emailExists = !!data.exists;
            validateField('company_email');
        })
        .catch(function() {});
});

document.getElementById('companyRegisterForm').addEventListener('submit', function(e) {
    var valid = true;

    Object.keys(validators).forEach(function(id) {
        if (!validateField(id)) valid = false;
    });

    if (!valid) {
        e.preventDefault();
        var firstErr = document.querySelector('.js-field-error');
        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endpush

@endsection
