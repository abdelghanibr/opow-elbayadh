@extends('layouts.app')

@section('title', 'تسجيل نادي رياضي جديد')

@section('content')

<div class="club-register-bg">

    <div class="club-register-box">

        <!-- Header -->
        <div class="text-center mb-3">
            <img src="{{ asset('images/djs-logo.png') }}" width="90">
            <h3 class="fw-bold mt-2">تسجيل نادي رياضي</h3>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('club.register.post') }}" id="clubRegisterForm" enctype="multipart/form-data" novalidate>
            @csrf
                @if($selectedComplex)
    <div class="alert alert-info text-center fw-bold">
        التسجيل في المركب: <span class="text-primary">{{ $selectedComplex->nom }}</span>
        <br>
        <small>رقم المركب: {{ $selectedComplex->id }}</small>
    </div>
@endif
            <div class="row g-4">

                <!-- Club Name -->
                <div class="col-md-6">
                    <label class="form-label">اسم النادي</label>
                    <input type="text" name="name" id="club_name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" id="club_email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <!-- Complex -->
 
<input type="hidden" name="complex_id" value="{{ $complexId }}">

                <!-- Agreement Number -->
                <div class="col-md-6">
                    <label class="form-label">رقم الاعتماد</label>
                    <input type="text" name="numero_agrement" id="club_agrement" value="{{ old('numero_agrement') }}"
                           class="form-control @error('numero_agrement') is-invalid @enderror" required>
                    @error('numero_agrement')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <!-- Expiration Date -->
                <div class="col-md-6">
                    <label class="form-label">تاريخ انتهاء الاعتماد</label>
                    <input type="date" name="date_expiration" id="club_date_expiration" value="{{ old('date_expiration') }}"
                           class="form-control @error('date_expiration') is-invalid @enderror" required>
                    @error('date_expiration')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <!-- Attachments -->
                <div class="col-md-12">
                    <label class="form-label">نسخة من وثيقة الاعتماد 📎</label>
                    <input type="file" name="attachments[]" id="club_attachments" multiple
                           class="form-control @error('attachments') is-invalid @enderror">
                    @error('attachments')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <!-- Password -->
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" id="club_password"
                           class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" id="club_password_confirmation" class="form-control" required>
                </div>

<div class="mt-3">
    <label class="fw-bold d-block mb-1">🔐 التحقق أنني لست روبوت :</label>

    <p class="text-muted mb-2">
        يرجى كتابة الكلمة التالية كما هي تماماً:
    </p>

    <!-- الكلمة داخل إطار جميل -->
    <div style="
        border: 2px dashed #007bff;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 1.3rem;
        font-weight: bold;
        text-align: center;
        background: #f8f9fa;
        color: #007bff;
        letter-spacing: 2px;
        margin-bottom: 15px;
        
    ">
        {{ $correctWord }}
    </div>

    <input type="text" 
           name="captcha_word" 
           id="club_captcha"
           class="form-control text-center"
           placeholder="اكتب الكلمة هنا" 
           required>

    @error('captcha_word')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>


                <!-- Privacy Zone -->
                <div class="privacy-zone mt-4">
                    <div class="privacy-header">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>حماية المعطيات الشخصية</span>
                    </div>

                    <div class="privacy-content">
                        <p>
                            أوافق على جمع ومعالجة بياناتي طبقًا للقانون الجزائري رقم 18-07
                            الخاص بحماية الأشخاص في معالجة المعطيات الشخصية.
                        </p>

                        <div class="form-check mt-3">
                            <input class="form-check-input @error('privacy_policy') is-invalid @enderror"
                                   type="checkbox" name="privacy_policy" id="club_privacy" value="1" required>

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
            لديك حساب؟ <a href="{{ route('club.login') }}">تسجيل الدخول</a>
        </p>

    </div>

</div>

{{-- Inline CSS --}}
<style>

.club-register-bg {
    background:#e8f5e9;
    padding:30px;
}

.club-register-box {
    background:white;
    width:95%;
    max-width:900px;
    margin:auto;
    padding:40px;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.12);
}

/* PRIVACY ZONE */
.privacy-zone {
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

.privacy-link {
    color:#a7f3d0;
    font-weight:900;
    text-decoration:underline;
}

.privacy-link:hover {
    color:white;
}

</style>

@push('js')
<script>
var checkEmailUrl = "{{ route('auth.check-email') }}";

document.getElementById('club_email').addEventListener('blur', function() {
    var email = this.value.trim();
    if (!email) return;
    fetch(checkEmailUrl + '?email=' + encodeURIComponent(email))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var el = document.getElementById('club_email');
            var existing = el.parentNode.querySelector('.email-exists-error');
            if (existing) existing.remove();
            if (data.exists) {
                var div = document.createElement('div');
                div.className = 'form-error email-exists-error';
                div.textContent = 'هذا البريد الإلكتروني مسجل بالفعل.';
                el.parentNode.appendChild(div);
                el.classList.add('is-invalid');
            }
        });
});

document.getElementById('clubRegisterForm').addEventListener('submit', function(e) {
    var valid = true;
    var errors = [];

    var name = document.getElementById('club_name').value.trim();
    if (!name || name.length < 3) {
        errors.push('اسم النادي مطلوب (3 أحرف على الأقل).');
        valid = false;
    }

    var email = document.getElementById('club_email').value.trim();
    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRe.test(email)) {
        errors.push('أدخل بريد إلكتروني صحيح.');
        valid = false;
    }

    var agrement = document.getElementById('club_agrement').value.trim();
    if (!agrement) {
        errors.push('رقم الاعتماد مطلوب.');
        valid = false;
    }

    var dateExp = document.getElementById('club_date_expiration').value;
    if (!dateExp) {
        errors.push('تاريخ انتهاء الاعتماد مطلوب.');
        valid = false;
    }

    var password = document.getElementById('club_password').value;
    if (!password || password.length < 8) {
        errors.push('كلمة المرور يجب أن تكون 8 أحرف على الأقل.');
        valid = false;
    }

    var confirm = document.getElementById('club_password_confirmation').value;
    if (password !== confirm) {
        errors.push('كلمتا المرور غير متطابقتين.');
        valid = false;
    }

    var captcha = document.getElementById('club_captcha').value.trim();
    if (!captcha) {
        errors.push('التحقق من الروبوت مطلوب.');
        valid = false;
    }

    var privacy = document.getElementById('club_privacy').checked;
    if (!privacy) {
        errors.push('يجب الموافقة على سياسة حماية البيانات.');
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
        alert(errors.join('\n'));
    }
});
</script>
@endpush

@endsection
