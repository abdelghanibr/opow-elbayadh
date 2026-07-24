@extends('layouts.app')

@section('content')

<style>
    body { font-family: "Cairo", sans-serif !important; }
    .welcome-box {
        background: #0A7355;
        color: white;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .btn-main {
        background:#1b5e20!important;
        color:#fff;
        border-radius:10px;
        padding:10px 20px;
        font-weight:700;
        display: inline-block;
        margin-top: 15px;
    }
    .btn-main:hover {
        background:#0b3b14!important;
    }
    .info-box {
        margin-top: 25px;
        background: #ffffff;
        border: 1px solid #d6f5e1;
        padding: 20px;
        border-radius: 14px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
</style>

<div class="container py-5" style="direction: rtl; text-align:right">

    <div class="welcome-box mb-4">
        <h2>🎉 أهلاً {{ Auth::user()->name }}</h2>
        <p class="mt-2">لقد تم إعادة تعيين كلمة المرور بنجاح. يمكنك الآن التوجه إلى لوحة التحكم الخاصة بك.</p>
    </div>

    <div class="info-box">
        @php
            $type = Auth::user()->type;
        @endphp

        @if($type == 'person')
            <a href="{{ route('person.dashboard') }}" class="btn-main">الذهاب إلى لوحة التحكم 👤</a>
        @elseif($type == 'club')
            <a href="{{ route('club.dashboard') }}" class="btn-main">الذهاب إلى لوحة تحكم النادي ⚽</a>
        @elseif($type == 'company')
            <a href="{{ route('entreprise.dashboard') }}" class="btn-main">إدارة حساب الشركة 🏢</a>
        @elseif($type == 'admin')
            <a href="{{ route('admin.dashboard') }}" class="btn-main">لوحة التحكم الإدارية 🛠️</a>
        @endif
    </div>

    <div class="text-center mt-4">
        <form id="logout-form"
              action="{{ route($type . '.logout') }}"
              method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-danger px-4">تسجيل الخروج 🚪</button>
        </form>
    </div>

</div>

@endsection
