@extends('layouts.app')

@section('title', 'تسجيل دخول مؤسسة')

@section('content')

<div class="login-bg-entreprise">

    <div class="login-box-entreprise">

        <!-- Header -->
        <div class="header-card-entreprise">
            <img src="{{ asset('images/djs-logo.png') }}" alt="Logo">
            <p class="fw-bold mt-2">تسجيل دخول المؤسسات - ولاية البيض</p>
        </div>

        <h3 class="entreprise-title">تسجيل دخول مؤسسة</h3>

        <form method="POST" action="{{ route('entreprise.login.post') }}">
            @csrf

            <label>البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" required>

            <label class="mt-3">كلمة المرور</label>
            <input type="password" name="password" class="form-control" required>

            {{-- reCAPTCHA --}}
            @if (!app()->environment('local'))
            <label class="mt-3">التحقق</label>
            <div class="captcha-wrapper">
                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
            </div>
            @endif

            {{-- Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2 mt-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit" class="btn-login-entreprise">دخول</button>

        </form>

       

        <div class="text-center mt-2">
            <a href="{{ route('password.request') }}" class="forgot-link-entreprise">
                نسيت كلمة المرور؟
            </a>
        </div>

    </div>
</div>

@if (!app()->environment('local'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

{{-- CSS داخل الـ Blade --}}
<style>

    /* 🌟 خلفية ذهبية */
    .login-bg-entreprise {
        font-family: "Cairo", sans-serif;
        background: linear-gradient(135deg, #8A6A0A, #cfa200);
        min-height: calc(100vh - 80px);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 25px;
    }

    /* 🟨 صندوق */
    .login-box-entreprise {
        width: 95%;
        max-width: 450px;
        background: #ffffff;
        border-radius: 20px;
        padding: 35px 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18);
    }

    /* الهيدر */
    .header-card-entreprise {
        background: linear-gradient(135deg, #fff4d0, #ffe89a);
        border-radius: 15px;
        padding: 15px;
        text-align: center;
        margin-bottom: 25px;
    }

    .header-card-entreprise img {
        height: 60px;
    }

    /* العنوان */
    .entreprise-title {
        font-weight: 800;
        color: #a37e00;
        text-align: center;
        margin-bottom: 15px;
    }

    /* الحقول */
    input.form-control {
        height: 48px;
        border-radius: 12px;
    }

    /* زر الدخول */
    .btn-login-entreprise {
        width: 100%;
        padding: 12px;
        border: none;
        background: #c09600;
        color: #fff;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        margin-top: 15px;
    }

    .btn-login-entreprise:hover {
        background: #826a00;
    }

    /* الروابط */
    .footer-text-entreprise a {
        font-weight: 700;
        color: #0057b3;
        text-decoration: none;
    }

    .footer-text-entreprise a:hover {
        text-decoration: underline;
    }

    .forgot-link-entreprise {
        font-weight: 700;
        color: #0d47a1;
        text-decoration: none;
    }

    .forgot-link-entreprise:hover {
        text-decoration: underline;
    }

    /* الكابتشا */
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
