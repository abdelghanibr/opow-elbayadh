@extends('layouts.app')

@section('title', 'تسجيل دخول الإدارة')

@section('content')

<div class="login-bg">
    <div class="login-box">

        <!-- Header -->
        <div class="header-card">
            <img src="{{ asset('images/djs-logo.png') }}" alt="Logo">
            <p class="fw-bold mt-2">لوحة تحكم الإدارة</p>
        </div>

        <h3>تسجيل الدخول</h3>

        <!-- Login Form -->
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <label>البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" required>

            <label class="mt-3">كلمة المرور</label>
            <input type="password" name="password" class="form-control" required>

            {{-- reCAPTCHA --}}
            @if (!app()->environment('local'))
            <label class="mt-3">التحقق</label>
            <div class="captcha-wrapper">
                <div class="g-recaptcha"
                     data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}">
                </div>
            </div>
            @endif

            {{-- Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2 mt-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit" class="btn-login">دخول</button>

            <div class="text-center mt-3">
                <a href="{{ route('password.request') }}" class="forgot-link">
                    نسيت كلمة المرور؟
                </a>
            </div>

        </form>

    </div>
</div>

@if (!app()->environment('local'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

{{-- CSS داخل نفس الـ Blade --}}
<style>

    /* خلفية الصفحة */
    .login-bg {
        font-family: "Cairo", sans-serif;
        background: linear-gradient(135deg, #6a0000, #b60000);
        min-height: calc(100vh - 80px);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 25px;
    }

    /* صندوق تسجيل الدخول */
    .login-box {
        width: 95%;
        max-width: 450px;
        background: #ffffff;
        border-radius: 20px;
        padding: 35px 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }

    /* رأس الصندوق */
    .header-card {
        background: #ffe2e2;
        border-radius: 15px;
        padding: 15px;
        text-align: center;
        margin-bottom: 25px;
    }

    .header-card img {
        height: 60px;
    }

    /* عنوان رئيسي */
    h3 {
        font-weight: 800;
        color: #7a0000;
        text-align: center;
        margin-bottom: 15px;
    }

    /* الحقول */
    input.form-control {
        height: 48px;
        border-radius: 12px;
    }

    /* زر الدخول */
    .btn-login {
        width: 100%;
        padding: 12px;
        border: none;
        background: #7a0000;
        color: #fff;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        margin-top: 15px;
    }

    .btn-login:hover {
        background: #530000;
    }

    /* رابط نسيان كلمة المرور */
    .forgot-link {
        color: #0d47a1;
        font-weight: bold;
        text-decoration: none;
    }

    .forgot-link:hover {
        text-decoration: underline;
    }

    /* غلاف الكابتشا */
    .captcha-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    /* تصغير الكابتشا على الهاتف */
    @media (max-width: 420px) {
        .g-recaptcha {
            transform: scale(0.85);
            transform-origin: center;
        }
    }

</style>

@endsection
