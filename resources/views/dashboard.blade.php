@extends('layouts.app')

@section('content')
<style>
    body { font-family: "Cairo", sans-serif !important; }
    .dash-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    .dash-card {
        border-radius: 14px;
        padding: 20px;
        background: #f8fdf9;
        border: 1px solid #d6f5e1;
        text-align: center;
        transition: .25s;
    }
    .dash-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.1);
    }
    .btn-main {
        background: #1b5e20;
        color: #fff;
        border-radius: 10px;
        padding: 8px 18px;
        font-weight: 700;
    }
    .alert-custom {
        border-radius: 10px;
        background: #fff4d4;
        border-left: 5px solid #ffca28;
    }
    .status-box {
        border-radius: 12px;
        padding: 20px;
        margin-top: 25px;
        font-size: 17px;
    }
</style>

<div class="container py-4" style="direction: rtl; text-align:right">

    <!-- Welcome Section -->
    <div class="dash-box mb-4" style="background:#1b5e20; color:white;">
        <h3 class="text-center mb-2">👋 أهلاً {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</h3>
        <p class="text-center">
            مرحباً بك في لوحة التحكم الخاصة بك لإدارة معلوماتك والاستفادة من خدمات المنصة.
            <br>يرجى استكمال بياناتك الشخصية للتمكن من التسجيل في الأنشطة.
        </p>
        <div class="text-center mt-3">
            <a href="{{ route('profile.step', 1) }}" class="btn btn-light px-4">استكمال البيانات</a>
        </div>
    </div>


    <!-- Cards Section -->
    <div class="row g-3">

        <div class="col-md-3">
            <div class="dash-card">
                <h5>📄 ملفك الشخصي</h5>
                <p class="text-muted">{{ Auth::user()->firstname ? '✓ مكتمل' : '✘ غير مكتمل' }}</p>
                <a href="{{ route('profile.step', 1) }}" class="btn btn-main btn-sm">أكمل الآن</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dash-card">
                <h5>⭐ الأنشطة المقترحة</h5>
                <p class="text-muted">أنشطة متاحة للتسجيل</p>
                <a href="{{ route('activities.index') }}" class="btn btn-main btn-sm">عرض الأنشطة</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dash-card">
                <h5>📌 أنشطتك</h5>
                <p class="text-muted">{{ $registeredActivities ?? 0 }} نشاط</p>
                <a href="{{ route('my.activities') }}" class="btn btn-main btn-sm">مشاهدة</a>
            </div>
        </div>

    </div>


    <!-- Dossier Status Section -->
<!-- Dossier Status Section -->
<div class="dash-box mt-4">
    <h4 class="mb-3">📌 حالة ملفك</h4>

    {{-- إذا يوجد دوسيي --}}
    @if($dossier)

        {{-- التحقق من إكمال الوثائق --}}
        @if(!Auth::user()->photo || !Auth::user()->birth_certificate)
            <div class="alert alert-info status-box">
                ⚠ ملفك غير مكتمل!
                <br>يرجى رفع الوثائق المطلوبة لاستكمال معالجة الطلب.
                <br>
                <a href="{{ route('profile.step', 4) }}" class="btn btn-primary btn-sm mt-2">
                    📤 استكمال رفع الوثائق
                </a>
            </div>

        {{-- حالة قبول الدوسيي --}}
        @elseif($dossier->etat == 'Validé')
            <div class="alert alert-success status-box">
                ✔ تم قبول ملفك! يمكنك الآن التسجيل في الأنشطة 🎉
            </div>

        {{-- حالة رفض الدوسيي --}}
        @elseif($dossier->etat == 'Refusé')
            <div class="alert alert-danger status-box">
                ❌ تم رفض ملفك. يرجى تعديل الوثائق وإعادة الرفع.
                <br>
                <a href="{{ route('profile.step', 4) }}" class="btn btn-light btn-sm mt-2">
                    ✏️ إعادة رفع الوثائق
                </a>
            </div>

        {{-- حالة قيد الدراسة --}}
        @else
            <div class="alert alert-warning status-box">
                ⏳ ملفك قيد الدراسة حالياً 🔔
            </div>
        @endif

    {{-- إذا لا يوجد دوسيي بعد --}}
    @else
        <div class="alert alert-info status-box">
            ⚠ لم تقم بإرسال ملفك بعد!
            <br>
            <a href="{{ route('profile.step', 1) }}" class="btn btn-primary btn-sm mt-2">
                🚀 أكمل البيانات الآن
            </a>
        </div>
    @endif

</div>








</div>
@endsection
