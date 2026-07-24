@extends('layouts.app')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="{{ asset('css/welcome.css') }}" rel="stylesheet">

@php
    if (app()->environment('local')) {
        $storageUrl = '/storage';
    } else {
        $storageUrl = rtrim(env('PUBLIC_STORAGE_URL'), '/');
    }

    $wilayaAr      = $wilayaAr ?? 'البيض';
    $wilayaFr      = $wilayaFr ?? 'EL-BAYADH';
    $officeShort   = $officeShort ?? config('app.name');
    $officeLabelFr = $officeLabelFr ?? 'Office du Parc Omnisports de la wilaya de ' . $wilayaFr;
    $contactEmail  = $contactEmail ?? 'contact@opow-elbayadh.dz';
    $contactPhone  = $contactPhone ?? '049613680';
    $contactPlace  = $contactPlace ?? 'ديوان المركب المتعدد الرياضات لولاية ' . $wilayaAr;

    $activeNews   = $news->where('is_active', 1);
    $activeEvents = $events->where('is_active', 1);
    $matchesCount = $matchesCount ?? 0;
@endphp

<section class="opow-hero-slider" id="top">

    {{-- صور الخلفية المتحركة --}}
    <div class="opow-slide-track">
        <div class="opow-slide"
             style="background-image: url('{{ asset('images/activities/1.jpg') }}');"></div>

        <div class="opow-slide"
             style="background-image: url('{{ asset('images/activities/2.jpg') }}');"></div>

        <div class="opow-slide"
             style="background-image: url('{{ asset('images/activities/3.jpg') }}');"></div>

        <div class="opow-slide"
             style="background-image: url('{{ asset('images/activities/4.jpg') }}');"></div>
    </div>

{{-- القائمة العلوية + الإحصائيات --}}
<div class="opow-topbar">

    <div class="opow-topbar-menu">
        <a href="#top" class="opow-topbar-link">
            <i class="fa-solid fa-house"></i>
            الرئيسية
        </a>

        <a href="#facilities" class="opow-topbar-link">
            <i class="fa-solid fa-dumbbell"></i>
            المنشآت
        </a>
<a href="#stats" class="opow-topbar-link">
    <i class="fa-solid fa-chart-simple"></i>
    الإحصائيات
</a>
        <a href="#news" class="opow-topbar-link">
            <i class="fa-solid fa-newspaper"></i>
            المستجدات
        </a>

        <a href="#events" class="opow-topbar-link">
            <i class="fa-solid fa-calendar-days"></i>
            الفعاليات
        </a>

        <a href="#activities-section" class="opow-topbar-link">
            <i class="fa-solid fa-person-running"></i>
            الأنشطة
        </a>

        <a href="#contact" class="opow-topbar-link">
            <i class="fa-solid fa-address-book"></i>
            التواصل
        </a>
    </div>



</div>

    {{-- محتوى فوق الصور --}}
    <div class="opow-hero-content">
        <div class="container">

            <div class="row justify-content-center align-items-center">

                <div class="col-12 col-md-10 col-lg-7 col-xl-6">

                    <div class="opow-identity-card text-center">

                        {{-- Logo au milieu --}}
                        <div class="opow-logo-box">
                            <img src="{{ asset('images/djs-logo.png') }}" alt="Logo">
                        </div>

                        <div class="opow-official-text-card">

                            <div class="opow-republic">
                                الجمهورية الجزائرية الديمقراطية الشعبية
                            </div>

                            <div class="opow-ministry">
                                وزارة الرياضة
                            </div>

                            <div class="opow-office-ar">
                                ديوان المركب المتعدد الرياضات لولاية {{ $wilayaAr }}
                            </div>

                            <div class="opow-office-fr">
                                {{ $officeLabelFr }}
                            </div>

                            <p class="opow-platform-desc">
                                منصة إلكترونية رسمية وحديثة لتنظيم الأنشطة الرياضية، متابعة المنخرطين،
                                حجز المرافق والمنشآت عن بعد، وتسهيل الوصول إلى الخدمات الرياضية بطريقة منظمة وآمنة.
                            </p>
                        </div>

                        {{-- Actions داخل البطاقة --}}
                        <div class="opow-card-actions">
                            <a href="#facilities" class="opow-card-action green">
                                <i class="fa-solid fa-pen-to-square"></i>
                                ابدأ التسجيل
                            </a>

                            @if($matchesCount > 0)
                                <a href="{{ route('matches.public') }}" class="opow-card-action opow-ticket-flash">
                                    <i class="fa-solid fa-ticket"></i>
                                    شراء التذاكر
                                </a>
                            @endif

                            <a href="#events" class="opow-card-action outline">
                                <i class="fa-solid fa-calendar-check"></i>
                                الفعاليات
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</section>

            </div>
        </div>
    </div>
</section>

{{-- بطاقات الأنشطة مثل الصورة --}}
<section class="opow-activity-cards-zone" id="facilities">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <div class="col-12 col-md-6 col-lg-4">
                <div class="opow-activity-card">
                    <div class="opow-activity-img"
                         style="background-image: url('{{ asset('images/activities/2.jpg') }}');"></div>

                    <div class="opow-activity-body">
                        <h5>المسابح</h5>
                        <p>
                            فضاءات مخصصة للسباحة، التدريب، اللياقة المائية،
                            وتعليم السباحة ضمن أفواج منظمة.
                        </p>

                        <button class="btn btn-opow-primary w-100 open-complex-modal"
                                data-type="swimming"
                                data-bs-toggle="modal"
                                data-bs-target="#complexModal">
                            📝 تسجيل
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="opow-activity-card">
                    <div class="opow-activity-img"
                         style="background-image: url('{{ asset('images/activities/3.jpg') }}');"></div>

                    <div class="opow-activity-body">
                         <h5>القاعات الرياضية</h5>
                        <p>
                            برامج تدريبية ورياضية موجهة لمختلف الفئات العمرية
                            تحت إشراف مؤطرين مختصين.
                        </p>

                        <button class="btn btn-opow-green w-100 open-complex-modal"
                                data-type="hall"
                                data-bs-toggle="modal"
                                data-bs-target="#complexModal">
                            📝 تسجيل
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="opow-activity-card">
                    <div class="opow-activity-img"
                         style="background-image: url('{{ asset('images/activities/1.jpg') }}');"></div>

                    <div class="opow-activity-body">
                        <h5>الملاعب</h5>
                        <p>
                            منشآت رياضية مخصصة لكرة القدم والرياضات الجماعية
                            مع إمكانية الحجز حسب البرامج المتاحة.
                        </p>

                        <button class="btn btn-opow-primary w-100 open-complex-modal"
                                data-type="stadium"
                                data-bs-toggle="modal"
                                data-bs-target="#complexModal">
                            📝 تسجيل
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
{{-- إحصائيات المنصة --}}
<section class="opow-stats-zone" id="stats">
    <div class="container">
        <div class="opow-stats-panel">

            <div class="opow-stats-header">
                <div>
                    <h2>إحصائيات المنصة</h2>
                    <p>
                        مؤشرات عامة حول المنشآت، الأنشطة، التسجيلات والحجوزات.
                    </p>
                </div>

                <div class="opow-stats-badge">
                    <i class="fa-solid fa-chart-simple"></i>
                    بيانات محدثة
                </div>
            </div>

            <div class="row g-3">

                <div class="col-6 col-lg-3">
                    <div class="opow-stat-box">
                        <div class="opow-stat-icon blue">
                            <i class="fa-solid fa-water-ladder"></i>
                        </div>
                        <div class="opow-stat-value">
                            {{ $stats['swimming_complexes'] ?? 0 }}
                        </div>
                        <div class="opow-stat-label">
                            عدد المسابح
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="opow-stat-box">
                        <div class="opow-stat-icon green">
                            <i class="fa-solid fa-futbol"></i>
                        </div>
                        <div class="opow-stat-value">
                            {{ $stats['stadium_complexes'] ?? 0 }}
                        </div>
                        <div class="opow-stat-label">
                            عدد الملاعب
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="opow-stat-box">
                        <div class="opow-stat-icon blue">
                            <i class="fa-solid fa-dumbbell"></i>
                        </div>
                        <div class="opow-stat-value">
                            {{ $stats['hall_complexes'] ?? 0 }}
                        </div>
                        <div class="opow-stat-label">
                            عدد القاعات
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="opow-stat-box">
                        <div class="opow-stat-icon green">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div class="opow-stat-value">
                            {{ $stats['activities_count'] ?? 0 }}
                        </div>
                        <div class="opow-stat-label">
                            عدد الأنشطة
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="opow-stat-box">
                        <div class="opow-stat-icon blue">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="opow-stat-value">
                            {{ $stats['registered_count'] ?? 0 }}
                        </div>
                        <div class="opow-stat-label">
                            عدد المسجلين
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="opow-stat-box">
                        <div class="opow-stat-icon green">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div class="opow-stat-value">
                            {{ $stats['reservations_count'] ?? 0 }}
                        </div>
                        <div class="opow-stat-label">
                            عدد الحجوزات
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="opow-stat-box">
                        <div class="opow-stat-icon blue">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                        <div class="opow-stat-value">
                            {{ $stats['news_count'] ?? 0 }}
                        </div>
                        <div class="opow-stat-label">
                            المستجدات المنشورة
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="opow-stat-box">
                        <div class="opow-stat-icon green">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div class="opow-stat-value">
                            {{ $stats['events_count'] ?? 0 }}
                        </div>
                        <div class="opow-stat-label">
                            الفعاليات النشطة
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

{{-- الأنشطة الرياضية --}}
<section class="opow-section" id="activities-section">
    <div class="container">

        <h2 class="opow-section-title">
            🏃 الأنشطة الرياضية
        </h2>

        <div class="row g-4">
            @forelse($activities as $activity)
                @php
                    $activityIcon = $activity->activity_category->icon ?? 'fa-dumbbell';
                    $activityColor = $activity->activity_category->color ?? '#082f57';
                @endphp

                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="opow-card">
                        <div class="opow-activity-icon-box" style="background: {{ $activityColor }}15; border: 2px solid {{ $activityColor }}30;">
                            <i class="fa-solid {{ $activityIcon }}" style="color: {{ $activityColor }}; font-size: 2rem;"></i>
                        </div>

                        <h6>{{ $activity->title }}</h6>

                        <p class="small">
                            {{ \Illuminate\Support\Str::limit(strip_tags($activity->description ?? ''), 120) }}
                        </p>

                        @if($activity->complexes && $activity->complexes->count())
                            <div class="opow-activity-meta mt-2">
                                <span class="badge bg-primary">
                                    <i class="fa-solid fa-building me-1"></i>
                                    {{ $activity->complexes->count() }} مركب
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">لا توجد أنشطة حالياً</p>
            @endforelse
        </div>

    </div>
</section>

{{-- الوصول السريع - أنواع الحسابات --}}
<section class="opow-section" id="access-section" style="background: linear-gradient(180deg, #f4f8fb 0%, #e8f0f7 100%);">
    <div class="container">

        <h2 class="opow-section-title">
            🔐 الوصول السريع
        </h2>

        <p class="text-center text-muted mb-5" style="max-width: 600px; margin-inline: auto;">
            اختر نوع حسابك للوصول إلى الخدمات المناسبة
        </p>

        <div class="row g-4 justify-content-center">

            {{-- فرد --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="opow-access-card">
                    <div class="opow-access-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <h5>منخرط / فرد</h5>
                    <p>سجل كمنخرط للاستفادة من مرافق المركب المتعدد الرياضات</p>
                    <div class="opow-access-actions">
                        <a href="{{ route('person.login') }}" class="btn btn-opow-primary">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            تسجيل الدخول
                        </a>
                        <a href="{{ route('person.register') }}" class="btn btn-opow-green">
                            <i class="fa-solid fa-user-plus"></i>
                            حساب جديد
                        </a>
                    </div>
                </div>
            </div>

            {{-- نادي --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="opow-access-card">
                    <div class="opow-access-icon" style="background: linear-gradient(135deg, #082f57, #0b3d70);">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h5>نادي رياضي</h5>
                    <p>سجّل ناديك الرياضي لإدارة المنخرطين والحجوزات</p>
                    <div class="opow-access-actions">
                        <a href="{{ route('club.login') }}" class="btn btn-opow-primary">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            تسجيل الدخول
                        </a>
                        <a href="{{ route('club.register') }}" class="btn btn-opow-green">
                            <i class="fa-solid fa-user-plus"></i>
                            تسجيل النادي
                        </a>
                    </div>
                </div>
            </div>

            {{-- شركة --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="opow-access-card">
                    <div class="opow-access-icon" style="background: linear-gradient(135deg, #12a86b, #0f8f5d);">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <h5>مؤسسة / شركة</h5>
                    <p>سجّل مؤسستك لتوفير الأنشطة الرياضية لموظفيك</p>
                    <div class="opow-access-actions">
                        <a href="{{ route('entreprise.login') }}" class="btn btn-opow-primary">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            تسجيل الدخول
                        </a>
                        <a href="{{ route('entreprise.register') }}" class="btn btn-opow-green">
                            <i class="fa-solid fa-building-circle-check"></i>
                            تسجيل المؤسسة
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

{{-- Modal اختيار المركبات --}}
<div class="modal fade" id="complexModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-lg-down modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title">
                    🏟️ المركبات المتاحة
                </h5>

                <button class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">
                    ✖ إغلاق
                </button>
            </div>

            <div class="modal-body p-3" id="complexModalBody">
                <p class="text-center text-muted">جاري التحميل...</p>
            </div>
        </div>
    </div>
</div>

{{-- شريط المستجدات --}}
<div class="container">
    @if($activeNews->count())
        <div class="opow-news-ticker">
            <span class="opow-news-label">
                <i class="fa-solid fa-newspaper ms-2"></i>
                المستجدات
            </span>

            <span class="opow-news-content">
                @foreach($activeNews as $item)
                    {{ $item->title }} — {{ \Illuminate\Support\Str::limit(strip_tags($item->content), 180) }}
                    @if(!$loop->last)
                        <span class="mx-3">|</span>
                    @endif
                @endforeach
            </span>
        </div>
    @endif
</div>

{{-- آخر المستجدات --}}
<section class="opow-section" id="news">
    <div class="container">

        <h2 class="opow-section-title">
            📰 آخر المستجدات
        </h2>

        <div class="row g-4">
            @forelse($activeNews as $item)
                @php
                    $newsImage = $item->image
                        ? (\Illuminate\Support\Str::startsWith($item->image, ['http://', 'https://'])
                            ? $item->image
                            : asset($item->image))
                        : asset('images/avatar-placeholder.png');
                @endphp

                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="opow-card">
                        <img src="{{ $newsImage }}"
                             alt="Photo"
                             class="opow-media-img">

                        <h6>{{ $item->title }}</h6>

                        <p class="small">
                            {{ \Illuminate\Support\Str::limit(strip_tags($item->content), 110) }}
                        </p>

                        <a href="{{ route('news.show', $item->id) }}"
                           class="btn btn-opow-primary w-100 mt-2">
                            اقرأ المزيد
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">لا توجد مستجدات</p>
            @endforelse
        </div>

    </div>
</section>

{{-- الفعاليات --}}
<section class="opow-section" id="events">
    <div class="container">

        <h2 class="opow-section-title">
            📅 الفعاليات القادمة
        </h2>

        <div class="row g-4">
            @forelse($activeEvents as $item)
                @php
                    $eventImage = $item->image
                        ? (\Illuminate\Support\Str::startsWith($item->image, ['http://', 'https://'])
                            ? $item->image
                            : asset($item->image))
                        : asset('images/avatar-placeholder.png');
                @endphp

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="opow-card">
                        <img src="{{ $eventImage }}"
                             alt="Event image"
                             class="opow-media-img">

                        <h6>
                            <i class="fa-solid fa-calendar-days ms-2"></i>
                            {{ $item->title }}
                        </h6>

                        <div class="mb-2">
                            <span class="badge bg-success">
                                من {{ \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') }}
                            </span>

                            @if($item->end_date)
                                <span class="badge bg-success">
                                    إلى {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>

                        <p class="small">
                            {{ \Illuminate\Support\Str::limit(strip_tags($item->description), 120) }}
                        </p>

                        <a href="{{ route('events.show', $item->id) }}"
                           class="btn btn-opow-primary w-100 mt-2">
                            تفاصيل الحدث
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">لا توجد فعاليات</p>
            @endforelse
        </div>

    </div>
</section>

{{-- Footer --}}
<footer class="opow-footer" id="contact">
    <div class="container">

        <div class="row g-4 align-items-start">

            <div class="col-12 col-md-4 text-md-end text-center">
                <h5>ديوان المركب المتعدد الرياضات</h5>

                <p>
                    {{ $officeLabelFr }}
                </p>

                <p class="fw-bold">
                    {{ $officeShort }}
                </p>
            </div>

            <div class="col-12 col-md-4 text-center">
                <h5>تواصل معنا</h5>

                <p>
                    <i class="fa-solid fa-location-dot"></i>
                    {{ $contactPlace }}
                </p>

                <p>
                    <i class="fa-solid fa-envelope"></i>
                    {{ $contactEmail }}
                </p>

                <p>
                    <i class="fa-solid fa-phone"></i>
                    {{ $contactPhone }}
                </p>

                <div class="opow-social mt-3">
                    <a href="https://www.facebook.com/share/14pKwUjNZRe/" aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a href="#" aria-label="X">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>

                    <a href="#" aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-4 text-md-start text-center">
                <h5>روابط مهمة</h5>

                <ul class="opow-footer-links">
                    <li><a href="#top">الصفحة الرئيسية</a></li>
                    <li><a href="#facilities">المنشآت الرياضية</a></li>
                    <li><a href="#activities-section">الأنشطة الرياضية</a></li>
                    <li><a href="#news">آخر المستجدات</a></li>
                    <li><a href="#events">الفعاليات القادمة</a></li>
                    <li><a href="{{ route('legal.terms') }}">الشروط والأحكام</a></li>
                    <li><a href="{{ route('legal.privacy') }}">سياسة الخصوصية</a></li>
                    <li>
                        <a href="https://msport.gov.dz/" target="_blank">
                            الموقع الرسمي للوزارة
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <div class="opow-footer-bottom text-center mt-4">
            © 2026 – جميع الحقوق محفوظة | ديوان المركب المتعدد الرياضات لولاية {{ $wilayaAr }}
        </div>

    </div>
</footer>

<script src="{{ asset('js/welcome1.js') }}"></script>

@endsection