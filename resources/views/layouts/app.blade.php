<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    
    


<meta name="description" content="الموقع الرسمي لديوان المركب المتعدد الرياضات للبيض، منصة رقمية لإدارة المنشآت الرياضية، الأنشطة، الاشتراكات، الحجوزات، التذاكر، الفعاليات والأخبار الرياضية بولاية البيض.">

<meta name="robots" content="index, follow">

<link rel="canonical" href="https://opow-elbayadh.dz/">

<meta property="og:title" content="ديوان المركب المتعدد الرياضات للبيض | {{ config('app.name') }}">
<meta property="og:description" content="الموقع الرسمي لديوان المركب المتعدد الرياضات للبيض لإدارة الأنشطة الرياضية، المنشآت، الحجوزات، الاشتراكات، التذاكر، الفعاليات والأخبار.">
<meta property="og:url" content="https://opow-elbayadh.dz/">
<meta property="og:type" content="website">
<meta property="og:site_name" content="ديوان المركب المتعدد الرياضات للبيض">
    
    
    

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    {{-- JS core --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <style>
        body{
            background:#f6f8fa;
            font-family:"Cairo",sans-serif!important;
        }

        /* ===============================
           COLOR THEMES PER USER TYPE
        =============================== */
        body.theme-admin{
            --main:#2563eb; --main-dark:#1e40af; --soft:#eff6ff;
        }
        body.theme-club{
            --main:#0a4f88; --main-dark:#083d65; --soft:#e7f1fb;
        }
        body.theme-company{
            --main:#6bbf59; --main-dark:#4e9f3d; --soft:#eaf7e6;
        }
        body.theme-person{
            --main:#7c3aed; --main-dark:#5b21b6; --soft:#f5f3ff;
        }

        /* ===============================
           NAVBAR
        =============================== */
        .navbar-modern{
            background:#fff;
            border-bottom:1px solid #e5e7eb;
            padding:12px 18px;
            box-shadow:0 4px 14px rgba(0,0,0,.08);
        }

        /* ===============================
           BUTTONS
        =============================== */
        .btn-nav{
            display:flex;
            align-items:center;
            gap:6px;
            padding:8px 14px;
            border-radius:14px;
            font-weight:800;
            font-size:14px;
            transition:.25s;
            box-shadow:0 6px 16px rgba(0,0,0,.10);
        }
        .btn-nav i{ font-size:15px; }
        .btn-nav:hover{
            transform:translateY(-2px);
            box-shadow:0 12px 28px rgba(0,0,0,.18);
        }

        .btn-main{
            background:var(--main);
            color:#fff;
        }
        .btn-main:hover{ background:var(--main-dark); }

        .btn-outline-main{
            border:2px solid var(--main);
            color:var(--main);
            background:#fff;
        }
        .btn-outline-main:hover{
            background:var(--main);
            color:#fff;
        }

        /* ===============================
           USER CHIP (2026 STYLE)
        =============================== */
        .user-chip{
            display:flex;
            align-items:center;
            gap:10px;
            padding:6px 14px;
            border-radius:18px;
            background:rgba(255,255,255,.9);
            backdrop-filter:blur(10px);
            box-shadow:0 8px 26px rgba(0,0,0,.12);
        }
        .user-avatar{
            width:42px;
            height:42px;
            border-radius:50%;
            background:linear-gradient(135deg,var(--main),var(--main-dark));
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:900;
            font-size:18px;
        }
        .user-name{
            font-weight:900;
            font-size:14px;
            color:#0f172a;
        }
        .user-role{
            font-size:12px;
            font-weight:700;
            color:#64748b;
        }

        main{ min-height:calc(100vh - 80px); }
        
        
        
/* -----------------------------
   RESPONSIVE MOBILE FIXES
--------------------------------*/
@media (max-width: 768px) {

    /* Navbar structure */
    .navbar-modern .container-fluid {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 12px;
        text-align: right;
    }

    /* Brand section */
    .navbar-brand img {
        width: 40px !important;
        height: 40px !important;
    }

    .navbar-brand div span:first-child {
        font-size: 13px !important;
    }
    .navbar-brand div span:last-child {
        font-size: 12px !important;
    }

    /* Buttons */
    .btn-nav {
        padding: 6px 10px !important;
        font-size: 12px !important;
        border-radius: 10px !important;
        box-shadow: none !important;
    }
    .btn-nav i {
        font-size: 13px !important;
    }

    /* Buttons container */
    .navbar-modern .d-flex.gap-2,
    .navbar-modern .d-flex.align-items-center {
        flex-wrap: wrap !important;
        gap: 6px !important;
        width: 100%;
        justify-content: flex-start !important;
    }

    /* User chip mobile */
    .user-chip {
        padding: 6px 10px !important;
        gap: 6px !important;
        box-shadow: 0 4px 10px rgba(0,0,0,.12) !important;
    }
    .user-avatar {
        width: 36px !important;
        height: 36px !important;
        font-size: 16px !important;
    }
    .user-name {
        font-size: 12px !important;
    }
    .user-role {
        font-size: 10px !important;
    }

    /* Logout button */
    form button.btn-nav {
        width: 100% !important;
        text-align: center;
    }

}




    </style>

    @stack('css')
</head>

@php
    $user = Auth::user();
    $themeClass = $user ? 'theme-'.$user->type : '';
@endphp

<body class="{{ $themeClass }}">

{{-- ================= NAVBAR ================= --}}
@if(Auth::check())

@php
    $user = Auth::user();

    // -------------------------
    // 🧭  Dashboard Route
    // -------------------------
    if ($user->type === 'admin') {
        // إذا كان لديه مجمع محدد
        if (!is_null($user->complex_id) && $user->complex_id != 0) {
            $dashboardRoute = route('admin.dashboard_complex', $user->complex_id);
        } else {
            $dashboardRoute = route('admin.dashboard');
        }
    } else {
        // مستخدمون آخرون
        $dashboardRoute = match($user->type){
            'club'       => route('club.dashboard'),
            'company',
            'entreprise' => route('entreprise.dashboard'),
            'person'     => route('person.dashboard'),
            default      => '#'
        };
    }

    // -------------------------
    // 👤 Profile Route
    // -------------------------
    $profileRoute = match($user->type){
        'admin'      => route('admin.profile.edit'),
        'club'       => route('club.profile.edit'),
        'person'     => route('person.profile.edit'),
        'company',
        'entreprise' => route('entreprise.profile.edit'),
        default      => '#'
    };

    // -------------------------
    // 📁 Dossier Route
    // -------------------------
    $dossierRoute = match($user->type){
        'club'       => route('club.dossier.index'),
        'company',
        'entreprise' => route('entreprise.dossier.index'),
        default      => '#'
    };

    // -------------------------
    // 🚪 Logout Route
    // -------------------------
    $logoutRoute = match($user->type){
        'admin'      => 'admin.logout',
        'club'       => 'club.logout',
        'company',
        'entreprise' => 'entreprise.logout',
        'person'     => 'person.logout',
        default      => 'logout'
    };
@endphp

<nav class="navbar navbar-modern">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        {{-- Logo --}}
          <a class="navbar-brand d-flex align-items-center gap-2" href="#top">
            <img src="{{ asset('images/djs-logo.png') }}" alt="Logo"
                 style="width:48px; height:48px; object-fit:contain;">
            <div class="d-flex flex-column lh-sm text-start">
                <span class="fw-bold" style="font-size:15px;">وزارة الرياضة - ولاية البيض</span>
                <span style="font-size:14px;">ديوان المركب المتعدد الرياضات</span>
            </div>
        </a>

        {{-- Actions --}}
        <div class="d-flex align-items-center gap-2 flex-wrap">

            <a href="{{ url('/') }}" class="btn btn-secondary btn-nav text-decoration-none">
                <i class="fa-solid fa-house"></i> الرئيسية
            </a>

            <a href="{{ $dashboardRoute }}" class="btn btn-main btn-nav">
                <i class="fa-solid fa-gauge"></i> لوحة التحكم
            </a>

            @if($dossierRoute !== '#')
            <a href="{{ $dossierRoute }}" class="btn btn-success btn-nav">
                <i class="fa-solid fa-folder-open"></i> الملف
            </a>
            @endif

            <a href="{{ $profileRoute }}" class="btn btn-outline-main btn-nav">
                <i class="fa-solid fa-user"></i> الحساب الشخصي
            </a>

            {{-- User chip --}}
            <div class="user-chip">
                <div class="user-avatar">
                    {{ mb_substr($user->name,0,1) }}
                </div>
                <div>
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-role">{{ ucfirst($user->type) }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route($logoutRoute) }}">
                @csrf
                <button class="btn btn-danger btn-nav">
                    <i class="fa-solid fa-right-from-bracket"></i> خروج
                </button>
            </form>

        </div>
    </div>
</nav>

@else
{{-- ===== VISITOR NAVBAR ===== --}}
<nav class="navbar navbar-modern">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Logo + Name -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="#top">
            <img src="{{ asset('images/djs-logo.png') }}" alt="Logo"
                 style="width:48px; height:48px; object-fit:contain;">
            <div class="d-flex flex-column lh-sm text-start">
                <span class="fw-bold" style="font-size:15px;">وزارة الرياضة - ولاية البيض</span>
                <span style="font-size:14px;">ديوان المركب المتعدد الرياضات</span>
            </div>
        </a>

        <!-- Login Buttons -->
        <div class="d-flex gap-2">

            {{-- Home --}}
            <a href="{{ url('/') }}" class="btn btn-light btn-nav">
                <i class="fa-solid fa-house"></i> الرئيسية
            </a>

            {{-- Person --}}
            <a href="{{ route('person.login') }}" class="btn btn-primary btn-nav">
                <i class="fa-solid fa-user"></i> حساب الرياضيين 
            </a>

            {{-- Club --}}
            <a href="{{ route('club.login') }}" class="btn btn-success btn-nav text-white">
                <i class="fa-solid fa-people-group"></i> حساب النوادي 
            </a>

            {{-- Entreprise --}}
            <a href="{{ route('entreprise.login') }}" class="btn btn-warning btn-nav text-white">
                <i class="fa-solid fa-building"></i>حساب المؤسسات 
            </a>

            {{-- Admin --}}
            <a href="{{ route('admin.login') }}" class="btn btn-danger btn-nav">
                <i class="fa-solid fa-shield-halved"></i> حساب الإدرة 
            </a>

        </div>

    </div>
</nav>
@endif


{{-- ========================================== --}}

<main class="py-4">
    @yield('content')
</main>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('js')


<script>
document.addEventListener("DOMContentLoaded", function () {
    flatpickr(".js-date-fr", {
        dateFormat: "Y-m-d",   // التخزين (Laravel / DB)
        altInput: true,
        altFormat: "d/m/Y",    // العرض للمستخدم
        allowInput: true,
        locale: "fr"


    
       
      
       
    });
});
</script>





</body>
</html>
