@extends('layouts.app')

@section('content')
<style>
    body {
        font-family: "Cairo", sans-serif !important;
        background: #f4f7fb;
    }

    .dash-header {
        background:
            radial-gradient(circle at top left, rgba(20,184,166,.35), transparent 35%),
            linear-gradient(135deg, #082f49, #075985);
        color: #fff;
        border-radius: 28px;
        padding: 28px;
        text-align: center;
        box-shadow: 0 16px 38px rgba(8,47,73,.22);
    }

    .dash-card {
        position: relative;
        display: block;
        height: 100%;
        min-height: 230px;
        border-radius: 24px;
        padding: 24px 18px 55px;
        text-align: right;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        border: 1px solid #e5eaf2;
        transition: .25s ease;
        cursor: pointer;
        box-shadow: 0 10px 26px rgba(15,23,42,.07);
        color: #0f172a;
        text-decoration: none;
        overflow: hidden;
    }

    .dash-card::before {
        content: "";
        position: absolute;
        top: -35px;
        left: -35px;
        width: 95px;
        height: 95px;
        border-radius: 50%;
        background: rgba(14,165,233,.10);
    }

    .dash-card::after {
        content: "";
        position: absolute;
        right: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #0ea5e9, #14b8a6);
    }

    .dash-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 36px rgba(15,23,42,.14);
        border-color: #38bdf8;
        color: #075985;
    }

    .dash-icon {
        width: 58px;
        height: 58px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        background: linear-gradient(135deg, #e0f2fe, #f0fdfa);
        margin-bottom: 14px;
        position: relative;
        z-index: 1;
    }

    .dash-title {
        font-weight: 900;
        font-size: 16px;
        margin-bottom: 9px;
        color: #082f49;
        position: relative;
        z-index: 1;
    }

    .dash-desc {
        font-size: 13px;
        color: #64748b;
        line-height: 1.8;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
        font-weight: 700;
    }

    .count-box {
        position: absolute;
        left: 16px;
        bottom: 16px;
        min-width: 42px;
        height: 28px;
        padding: 4px 12px;
        border-radius: 999px;
        background: #eef6ff;
        color: #075985;
        font-size: 13px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dbeafe;
        z-index: 2;
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


{{-- تأمين المنخرطين --}}
<div class="col-md-3">
    <a href="{{ route('admin.assurances.index') }}" class="dash-card text-decoration-none">
        <div class="dash-icon">🛡️</div>
        <div class="dash-title">تأمين المنخرطين</div>
        <p class="dash-desc">
            إضافة المنخرطين المؤمنين، متابعة التأمينات النشطة والمنتهية، وطباعة قوائم التأمين.
        </p>
        <div class="count-box">
            عدد التأمينات: {{ \App\Models\PersonAssurance::count() }}
        </div>
    </a>
</div>

        {{-- الفرق --}}
        <div class="col-md-3">
            <a href="{{ route('teams.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">🤼‍♂️</div>
                <div class="dash-title">الفرق</div>
                <p class="dash-desc">
                    إدارة الفرق والأصناف الرياضية الخاصة بالمؤسسة.
                </p>
                <div class="count-box">عدد الفرق: {{ \App\Models\Team::count() }}</div>
            </a>
        </div>

        {{-- فئات العمر --}}
        <div class="col-md-3">
            <a href="{{ route('age-categories.index') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">👶</div>
                <div class="dash-title">فئات العمر</div>
                <p class="dash-desc">
                    ضبط الفئات العمرية المعتمدة لتسجيل المنخرطين.
                </p>
                <div class="count-box">عدد الفئات: {{ \App\Models\AgeCategory::count() }}</div>
            </a>
        </div>

        {{-- حساب بدون ملف (محدود بمجمع هذا المدير) --}}
        <div class="col-md-3">
            <a href="{{ route('admin.accounts.no-dossier') }}" class="dash-card text-decoration-none">
                <div class="dash-icon">🚫</div>
                <div class="dash-title">حسابات بدون ملف</div>
                <p class="dash-desc">
                    حذف الحسابات المسجلة في هذا المجمع ولم تُقدّم أي ملف — فردي أو جماعي.
                </p>
                <div class="count-box">العدد: {{ $noDossierAccountsCount ?? 0 }}</div>
            </a>
        </div>

        {{-- البرنامج الأسبوعي للمنشأة --}}
        <div class="col-md-3">
            <a href="{{ route('admin.complex.programme', $complex->id) }}" class="dash-card text-decoration-none">
                <div class="dash-icon">📅</div>
                <div class="dash-title">البرنامج الأسبوعي للمنشأة</div>
                <p class="dash-desc">
                    عرض الأفواج النشطة في أسبوع واحد محدد، مع توقيت كل حصة وعدد الحجوزات لكل فوج.
                </p>
                <div class="count-box">أفواج نشطة: {{ $activeGroupsCount ?? 0 }}</div>
            </a>
        </div>

    </div>
</div>
@endsection
