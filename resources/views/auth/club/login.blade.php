@extends('layouts.app')

@section('title', 'تسجيل دخول نادي')

@section('content')

<div class="login-bg-club">
    <div class="login-box-club">

        <!-- Header -->
        <div class="header-card-club">
            <img src="{{ asset('images/djs-logo.png') }}" alt="Logo">
            <p class="fw-bold mt-2">دخول النوادي الرياضية - ولاية البيض</p>
        </div>

        <h3 class="club-title">تسجيل دخول نادي</h3>

        <form method="POST" action="{{ route('club.login.post') }}">
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

            <button type="submit" class="btn-login-club">دخول</button>

        </form>

     

        <div class="text-center mt-2">
            <a href="{{ route('password.request') }}" class="forgot-link-club">
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
    .login-bg-club {
        font-family: "Cairo", sans-serif;
        background: linear-gradient(135deg, #0D4775, #007b55);
        min-height: calc(100vh - 80px);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 25px;
    }

    /* الصندوق */
    .login-box-club {
        width: 95%;
        max-width: 450px;
        background: #ffffff;
        border-radius: 20px;
        padding: 35px 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18);
    }

    /* الهيدر */
    .header-card-club {
        background: linear-gradient(135deg, #d4f7e4, #b6eed1);
        border-radius: 15px;
        padding: 15px;
        text-align: center;
        margin-bottom: 25px;
    }

    .header-card-club img {
        height: 60px;
    }

    /* العنوان */
    .club-title {
        font-weight: 800;
        color: #0A6C44;
        text-align: center;
        margin-bottom: 15px;
    }

    /* الحقول */
    input.form-control {
        height: 48px;
        border-radius: 12px;
    }

    /* زر الدخول */
    .btn-login-club {
        width: 100%;
        padding: 12px;
        border: none;
        background: #0A6C44;
        color: #fff;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        margin-top: 15px;
    }

    .btn-login-club:hover {
        background: #06492c;
    }

    /* الروابط */
    .footer-text-club a {
        font-weight: 700;
        color: #0057b3;
        text-decoration: none;
    }

    .footer-text-club a:hover {
        text-decoration: underline;
    }

    .forgot-link-club {
        font-weight: 700;
        color: #0d47a1;
        text-decoration: none;
    }

    .forgot-link-club:hover {
        text-decoration: underline;
    }

    /* كابتشا */
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
