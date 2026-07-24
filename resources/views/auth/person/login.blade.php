@extends('layouts.app')

@section('title', 'تسجيل دخول فرد')

@section('content')

<div class="login-bg-person">

    <div class="login-box-person">

        <!-- Header -->
        <div class="header-card-person">
            <img src="{{ asset('images/djs-logo.png') }}" alt="Logo">
            <p class="fw-bold mt-2">منصة الرياضة للجميع - ولاية البيض</p>
        </div>

        <h3 class="person-title">تسجيل دخول فرد</h3>

        <!-- Login Form -->
        <form method="POST" action="{{ route('person.login.post') }}">
            @csrf

            <label>البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control"
                   placeholder="example@mail.com" required>

            <label class="mt-3">كلمة المرور</label>
            <input type="password" name="password" class="form-control"
                   placeholder="••••••" required>

            <!-- Captcha -->
            @if (!app()->environment('local'))
            <label class="mt-3">التحقق</label>
            <div class="captcha-wrapper">
                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
            </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2 mt-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit" class="btn-login-person">دخول</button>

        </form>

        

        <div class="text-center mt-2">
            <a href="{{ route('password.request') }}" class="forgot-link-person">
                نسيت كلمة المرور؟
            </a>
        </div>

    </div>
</div>

@if (!app()->environment('local'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

{{-- CSS داخل نفس الـ Blade --}}
<style>

    /* خلفية */
    .login-bg-person {
        font-family: "Cairo", sans-serif;
        background: linear-gradient(135deg, #00416A, #0D775C);
        min-height: calc(100vh - 80px);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 25px;
    }

    /* الصندوق */
    .login-box-person {
        width: 95%;
        max-width: 450px;
        background: #ffffff;
        border-radius: 20px;
        padding: 35px 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18);
    }

    /* الهيدر */
    .header-card-person {
        background: linear-gradient(135deg, #e0f2e9, #d8f3df);
        border-radius: 15px;
        padding: 15px;
        text-align: center;
        margin-bottom: 25px;
    }

    .header-card-person img {
        height: 60px;
    }

    /* العنوان */
    .person-title {
        font-weight: 800;
        color: #1b5e20;
        text-align: center;
        margin-bottom: 15px;
    }

    /* الحقول */
    input.form-control {
        height: 48px;
        border-radius: 12px;
    }

    /* زر الدخول */
    .btn-login-person {
        width: 100%;
        padding: 12px;
        border: none;
        background: #1b5e20;
        color: #fff;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        margin-top: 12px;
    }

    .btn-login-person:hover {
        background: #0d3d14;
    }

    /* الروابط */
    .footer-text-person {
        text-align: center;
        font-size: 0.95rem;
    }

    .footer-text-person a {
        color: #0d47a1;
        font-weight: 700;
        text-decoration: none;
    }

    .footer-text-person a:hover {
        text-decoration: underline;
    }

    .forgot-link-person {
        font-weight: 700;
        color: #0d47a1;
        text-decoration: none;
    }

    .forgot-link-person:hover {
        text-decoration: underline;
    }

    /* Captcha */
    .captcha-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 6px;
    }

    @media (max-width: 420px) {
        .g-recaptcha {
            transform: scale(0.85);
            transform-origin: center;
        }
    }

</style>

@endsection
