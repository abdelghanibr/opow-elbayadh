@extends('layouts.app')

@section('content')
<style>
    body {
        font-family: "Cairo", sans-serif !important;
        background: #f6f8fb;
    }

    .dash-header {
        background: linear-gradient(135deg, #9d1421, #c32635);
        color: #fff;
        border-radius: 18px;
        padding: 28px;
        text-align: center;
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .dash-card {
        display: block;
        height: 100%;
        min-height: 230px;
        border-radius: 18px;
        padding: 26px 18px;
        text-align: center;
        background: #ffffff;
        border: 2px solid #e8eef3;
        transition: .25s ease-in-out;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        color: #212529;
    }

    .dash-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.13);
        border-color: #0a4f88;
        color: #212529;
    }

    .dash-icon {
        font-size: 44px;
        margin-bottom: 12px;
        line-height: 1;
    }

    .dash-title {
        font-weight: 800;
        font-size: 16px;
        margin-bottom: 10px;
        color: #1f2d3d;
    }

    .dash-desc {
        font-size: 13px;
        color: #6c757d;
        line-height: 1.7;
        min-height: 44px;
        margin-bottom: 12px;
    }

    .count-box {
        background: #f1f7fc;
        padding: 7px 12px;
        font-size: 14px;
        margin-top: 10px;
        border-radius: 10px;
        font-weight: 700;
        border: 1px solid #d8e4ef;
        color: #0a4f88;
        display: inline-block;
    }
</style>

<div class="container py-4" style="direction: rtl; text-align:right;">

    <div class="dash-header mb-4">
        <h3 class="fw-bold mb-2">🎯 أهلاً بك مدير النظام {{ Auth::user()->name }}!</h3>
        <p class="mb-0">يمكنك هنا إدارة الملفات، الأنشطة، الحجوزات، التذاكر، والأعطال بسهولة</p>
    </div>

    <div class="row g-4">

        {{-- ملفات المشتركين --}}
        <div class="col-md-3">
            <a href="{{ route('admin.dossiers.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">🗂️</div>
                <div class="dash-title">دراسة الملفات</div>
                <p class="dash-desc">
                    مراجعة ملفات المنخرطين والنوادي والمصادقة عليها أو رفضها حسب الحالة.
                </p>
                <div class="count-box">الإجمالي: {{ $dossiersCount }}</div>
            </a>
        </div>

        {{-- النوادي الرياضية --}}
        <div class="col-md-3">
            <a href="{{ route('admin.clubs.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">🏊‍♂️</div>
                <div class="dash-title">النوادي الرياضية</div>
                <p class="dash-desc">
                    إدارة حسابات النوادي الرياضية ومتابعة بياناتها وملفاتها الإدارية.
                </p>
                <div class="count-box">عدد النوادي: {{ $clubsCount }}</div>
            </a>
        </div>

        {{-- المنخرطين --}}
        <div class="col-md-3">
            <a href="{{ route('persons.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">👥</div>
                <div class="dash-title">المنخرطين</div>
                <p class="dash-desc">
                    عرض وإدارة بيانات الأفراد المسجلين ومتابعة وضعية ملفاتهم وحجوزاتهم.
                </p>
                <div class="count-box">العدد: {{ $personsCount }}</div>
            </a>
        </div>

        {{-- التخصصات الرياضية --}}
        <div class="col-md-3">
            <a href="{{ route('admin.activities.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">🏋️‍♂️</div>
                <div class="dash-title">التخصصات الرياضية</div>
                <p class="dash-desc">
                    إضافة وتعديل التخصصات الرياضية المتاحة داخل المنشآت والمركبات.
                </p>
                <div class="count-box">العدد: {{ $activitiesCount }}</div>
            </a>
        </div>

        {{-- طاقة الاستيعاب --}}
        <div class="col-md-3">
            <a href="{{ route('admin.capacities.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">🏫</div>
                <div class="dash-title">طاقة الاستيعاب للمنشآت</div>
                <p class="dash-desc">
                    تحديد عدد الأماكن المتاحة لكل منشأة ونشاط لضبط الحجوزات بدقة.
                </p>
                <div class="count-box">العدد: {{ \App\Models\ComplexActivity::count() }}</div>
            </a>
        </div>

        {{-- الأفواج والتسعيرة --}}
        <div class="col-md-3">
            <a href="{{ route('admin.schedules.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">⏰</div>
                <div class="dash-title">إدارة الأفواج والتسعيرة</div>
                <p class="dash-desc">
                    تنظيم الأفواج، أيام الحصص، التوقيت، السعة، والأسعار المعتمدة.
                </p>
                <div class="count-box">عدد الأفواج: {{ \App\Models\Schedule::count() }}</div>
            </a>
        </div>

        {{-- توزيع الأفواج --}}
        <div class="col-md-3">
            <a href="{{ route('reservations.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">📝</div>
                <div class="dash-title">توزيع الأفواج</div>
                <p class="dash-desc">
                    متابعة الحجوزات وتوزيع المنخرطين على الأفواج حسب النشاط والتوقيت.
                </p>
                <div class="count-box">عدد الحجوزات: {{ $reservationsCount }}</div>
            </a>
        </div>

        {{-- أنواع المقاعد --}}
        <div class="col-md-3">
            <a href="{{ route('seat_types.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">🪑</div>
                <div class="dash-title">أنواع المقاعد</div>
                <p class="dash-desc">
                    إدارة أصناف المقاعد المعتمدة في الملاعب أو القاعات حسب التصنيف.
                </p>
                <div class="count-box">عدد الأنواع: {{ \App\Models\SeatType::count() }}</div>
            </a>
        </div>

        {{-- توزيع المقاعد --}}
        <div class="col-md-3">
            <a href="{{ route('complex_seats.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">🧮</div>
                <div class="dash-title">توزيع المقاعد</div>
                <p class="dash-desc">
                    ضبط عدد المقاعد وتوزيعها داخل المنشآت حسب النوع والموقع.
                </p>
                <div class="count-box">عدد المقاعد: {{ $seatsCount }}</div>
            </a>
        </div>

        {{-- المباريات --}}
        <div class="col-md-3">
            <a href="{{ route('matches.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">⚽</div>
                <div class="dash-title">المباريات</div>
                <p class="dash-desc">
                    إنشاء وإدارة المباريات وتحديد تاريخها والمنشأة الخاصة بها.
                </p>
                <div class="count-box">عدد المباريات: {{ $matchesCount }}</div>
            </a>
        </div>

        {{-- الفرق --}}
        <div class="col-md-3">
            <a href="{{ route('teams.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">🏆</div>
                <div class="dash-title">الفرق الرياضية</div>
                <p class="dash-desc">
                    إدارة الفرق المشاركة في المباريات وعرض بياناتها وشعارها.
                </p>
                <div class="count-box">عدد الفرق: {{ $teamsCount ?? 0 }}</div>
            </a>
        </div>

        {{-- التذاكر --}}
        <div class="col-md-3">
            <a href="{{ route('tickets.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">🎫</div>
                <div class="dash-title">التذاكر</div>
                <p class="dash-desc">
                    متابعة التذاكر المباعة والتحقق من حالتها وربطها بالمباريات.
                </p>
                <div class="count-box">عدد التذاكر: {{ $ticketsCount }}</div>
            </a>
        </div>

        {{-- توقيف المنشأة --}}
        <div class="col-md-3">
            <a href="{{ route('admin.pool-closures.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">⚠️</div>
                <div class="dash-title">توقيف المنشأة</div>
                <p class="dash-desc">
                    تسجيل أعطال المسبح أو فترات التوقف وعرض الحجوزات المتأثرة وتوليد التعويضات.
                </p>
                <div class="count-box">إدارة الأعطال</div>
            </a>
        </div>

    </div>
</div>
@endsection
