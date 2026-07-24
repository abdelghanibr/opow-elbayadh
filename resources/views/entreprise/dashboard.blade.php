@extends('layouts.app')

@section('content')
<style>
/* =========================================================
   GLOBAL
========================================================= */
body{
    font-family: "Cairo", sans-serif !important;
    background:#f7f9f7;
    color:#1f2937;
}

/* =========================================================
   VARIABLES – ENTREPRISE THEME
========================================================= */
:root{
    /* ===== Primary : Vert Pistache ===== */
    --ent-primary: #7d968aff;
    --ent-primary-dark:#4e9f3d;
    --ent-primary-soft:#edf8ea;

    /* ===== Secondary : Teal / Petrol ===== */
    --ent-secondary:#0f766e;
    --ent-secondary-dark:#115e59;
    --ent-secondary-soft:#e6f4f2;

    /* ===== Status ===== */
    --ent-success:#3fa34d;
    --ent-warning:#f59e0b;
    --ent-danger:#dc2626;

    /* ===== Neutral ===== */
    --ent-border:#e2eadf;
    --ent-muted:#6b7f6a;
    --ent-text:#1f2937;

    /* ===== Shape ===== */
    --ent-radius:18px;
    --ent-shadow:0 10px 28px rgba(0,0,0,.08);
}

/* =========================================================
   HEADER
========================================================= */
.dash-box{
    background:linear-gradient(
        135deg,
        var(--ent-primary),
        var(--ent-secondary)
    );
    color:#fff;
    border-radius:var(--ent-radius);
    padding:32px;
    box-shadow:var(--ent-shadow);
    text-align:center;
}

/* =========================================================
   GRID
========================================================= */
.cards-wrapper{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:24px;
    margin-top:24px;
}
@media(max-width:992px){
    .cards-wrapper{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:576px){
    .cards-wrapper{grid-template-columns:1fr;}
}

/* =========================================================
   CARD
========================================================= */
.club-players-card{
    background:#fff;
    border:1px solid var(--ent-border);
    border-radius:var(--ent-radius);
    padding:26px;
    box-shadow:var(--ent-shadow);
    display:flex;
    flex-direction:column;
    transition:.3s ease;
}
.club-players-card:hover{
    transform:translateY(-6px);
    box-shadow:0 18px 40px rgba(0,0,0,.12);
}

/* =========================================================
   CARD HEADER
========================================================= */
.card-header{
    display:flex;
    align-items:center;
    gap:14px;
    margin-bottom:18px;
}
.icon-circle{
    width:54px;
    height:54px;
    border-radius:50%;
    background:linear-gradient(
        135deg,
        var(--ent-secondary),
        var(--ent-primary)
    );
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
}
.card-header h5{
    margin:0;
    font-weight:800;
    color:var(--ent-primary-dark);
}
.card-header p{
    margin:0;
    font-size:14px;
    color:var(--ent-muted);
}

/* =========================================================
   STATS
========================================================= */
.card-stats{
    display:flex;
    justify-content:space-between;
    background:linear-gradient(
        135deg,
        var(--ent-primary-soft),
        var(--ent-secondary-soft)
    );
    border-radius:14px;
    padding:16px;
    margin-bottom:22px;
}
.stat{
    flex:1;
    text-align:center;
}
.stat .number{
    font-size:22px;
    font-weight:800;
    color:var(--ent-primary-dark);
}
.stat .label{
    font-size:13px;
    color:var(--ent-muted);
}

/* =========================================================
   BUTTON SYSTEM – FULL
========================================================= */

/* 🔵 Primary (إدارة / عرض رئيسي) */
.btn-primary,
.btn-club-primary,
.btn-manage{
    background:linear-gradient(
        135deg,
        var(--ent-primary),
        var(--ent-primary-dark)
    );
    color:#fff !important;
    border:none;
    font-weight:800;
    border-radius:14px;
    padding:10px 22px;
    box-shadow:0 6px 16px rgba(0,0,0,.15);
    transition:.25s;
}
.btn-primary:hover,
.btn-club-primary:hover,
.btn-manage:hover{
    background:linear-gradient(
        135deg,
        var(--ent-primary-dark),
        #191b19ff
    );
    transform:translateY(-2px);
}

/* 🟢 Success (حفظ / إرسال) */
.btn-success{
    background:linear-gradient(
        135deg,
        var(--ent-success),
        #2e7d32
    );
    color:#fff !important;
    border:none;
    font-weight:900;
    border-radius:14px;
    padding:10px 22px;
}
.btn-success:hover{
    background:linear-gradient(135deg,#2e7d32,#1b5e20);
}

/* 🟡 Warning (تعديل ملف / تنبيه) */
.btn-warning{
    background:linear-gradient(
        135deg,
        var(--ent-warning),
        #d97706
    );
    color:#fff !important;
    border:none;
    font-weight:800;
    border-radius:14px;
}
.btn-warning:hover{
    background:linear-gradient(135deg,#d97706,#b45309);
}

/* 🔴 Danger (حذف) */
.btn-danger{
    background:linear-gradient(
        135deg,
        var(--ent-danger),
        #991b1b
    );
    color:#fff !important;
    border:none;
    font-weight:900;
    border-radius:14px;
}
.btn-danger:hover{
    background:linear-gradient(135deg,#991b1b,#7f1d1d);
}

/* 🟦 Secondary (عرض / رجوع / تفاصيل) */
.btn-secondary,
.btn-outline-info{
    background:var(--ent-secondary-soft);
    color:var(--ent-secondary-dark);
    border:2px solid var(--ent-secondary);
    font-weight:800;
    border-radius:14px;
    padding:10px 20px;
    transition:.25s;
}
.btn-secondary:hover,
.btn-outline-info:hover{
    background:var(--ent-secondary);
    color:#fff;
}

/* 🟢 Outline Primary (عرض الملف / تحميل) */
.btn-outline-primary,
.btn-club-outline{
    border:2px solid var(--ent-primary);
    color:var(--ent-primary-dark);
    background:#fff;
    font-weight:800;
    border-radius:14px;
    padding:8px 18px;
    transition:.25s;
}
.btn-outline-primary:hover,
.btn-club-outline:hover{
    background:var(--ent-primary);
    color:#fff;
}

/* Small buttons (tables) */
.btn-sm{
    padding:6px 14px;
    font-size:13px;
    border-radius:12px;
}

/* =========================================================
   DOSSIER CARD
========================================================= */
.club-card{
    background:#fff;
    border:1px solid var(--ent-border);
    border-radius:var(--ent-radius);
    box-shadow:var(--ent-shadow);
    margin-top:26px;
}
.club-header{
    background:linear-gradient(
        135deg,
        var(--ent-secondary-dark),
        var(--ent-primary)
    );
    color:#fff;
    padding:16px 20px;
    border-radius:var(--ent-radius) var(--ent-radius) 0 0;
    font-weight:900;
}

/* =========================================================
   STATUS BOX
========================================================= */
.status-box{
    border-radius:16px;
    padding:18px;
    font-weight:800;
    box-shadow:var(--ent-shadow);
}

</style>

<div class="container py-4" style="direction: rtl; text-align:right">

    {{-- ===== Header ===== --}}
    <div class="dash-box mb-4">
        <h3 class="text-center mb-2">🏢 مرحباً {{ Auth::user()->name }}</h3>
        <p class="text-center">
            إدارة مؤسستك، موظفيك، وملفاتك الإدارية بكل سهولة
        </p>
        <p class="text-center" style="font-size:18px; font-weight:bold;">
        📍 المنشأة: {{ Auth::user()->complex->nom ?? '—' }} 
    </p>
    </div>

    {{-- ===== Cards Section ===== --}}
    <div class="cards-wrapper">

        {{-- 👥 مستخدمو / عمال المؤسسة --}}
        <div class="club-players-card">
            <div class="card-header">
                <div class="icon-circle">👥</div>
                <div>
                    <h5>أشخاص المؤسسة</h5>
                    <p>إدارة العمال والموظفين</p>
                </div>
            </div>

                <div class="card-stats">
            <div class="stat">
                <div class="number">{{ $playersCount }}</div>
                <div class="label">لاعبين</div>
            </div>
            <div class="stat">
                <div class="number">{{ $coachsCount }}</div>
                <div class="label">مدربين</div>
            </div>
            <div class="stat">
                <div class="number">{{ $managersCount }}</div>
                <div class="label">مسيرين</div>
            </div>
        </div>

            <a href="{{ route('entreprise.persons.index') }}" class="btn-manage">
                ⚙️ إدارة الأشخاص
            </a>
        </div>

        {{-- 📅 النشاطات --}}
        <div class="club-players-card">
            <div class="card-header">
                <div class="icon-circle">📅</div>
                <div>
                    <h5>النشاطات</h5>
                    <p>النشاطات والخدمات المتاحة</p>
                </div>
            </div>

            <div class="card-stats">
                <div class="stat">
                    <div class="number">—</div>
                    <div class="label">نشاطات</div>
                </div>
            </div>

            <a href="{{ route('activities.index') }}" class="btn-manage">
                استكشاف النشاطات
            </a>
        </div>

        {{-- 🎟️ الحجوزات --}}
        <div class="club-players-card">
            <div class="card-header">
                <div class="icon-circle">🎟️</div>
                <div>
                   <h5>🎟️ عدد حجوزاتي  </h5>
                    <p>حجوزات القاعات والخدمات</p>
                </div>
            </div>

            <div class="card-stats">
                <div class="stat">
                    <div class="number">{{ $totalReservations }}</div>
                    <div class="label">حجوزات</div>
                </div>
            </div>

            <a href="{{ route('reservation.my-reservations') }}" class="btn-manage">
                عرض الحجوزات
            </a>
        </div>

    </div>

    {{-- ===== 📁 Dossier Entreprise ===== --}}
   <div class="club-card">
    <div class="club-header p-3 fw-bold">
        📁 ملف النادي
    </div>

    <div class="p-3 text-center">
 <div class="dash-box mt-4">
    <h4 class="mb-3">📌 حالة ملفك</h4>

   @if($club)

    @php
        $attachments = json_decode($club->attachments ?? '[]', true);
        $hasFiles = is_array($attachments) && count($attachments) > 0;
          $hasNote  = !empty($club->note_admin);
    @endphp

    {{-- 🟡 حالة انتظار رفع الوثائق --}}
    @if(!$hasFiles)
        <div class="alert alert-info status-box">
            ⚠ ملفك غير مكتمل!
            <br>يرجى رفع الوثائق المطلوبة لإكمال معالجة الطلب.
            <br>
            <a href="{{ route('profile.step', 4) }}" class="btn btn-primary btn-sm mt-2">
                📤 استكمال رفع الوثائق
            </a>
        </div>

    {{-- 🟢 حالة القبول --}}
    @elseif($club->etat == 'approved')
        <div class="alert alert-success status-box">
            ✔ تم قبول ملفك! 🎉 يمكنك الآن الاستفادة من الخدمات
        </div>

    {{-- 🔴 حالة الرفض --}}
    @elseif($club->etat == 'rejected')
        <div class="alert alert-danger status-box">
            ❌ تم رفض ملفك. يرجى تعديل الوثائق وإعادة الرفع.
            <br>
             @if($hasNote)
                <hr>
                <strong>📝 ملاحظة الإدارة:</strong>
                <div class="mt-1 small">
                    {{ $club->note_admin }}
                </div>
            @endif
            </a>
        </div>

    {{-- 🕒 حالة قيد الدراسة --}}
    @else
        <div class="alert alert-warning status-box">
            ⏳ ملفك قيد الدراسة حالياً 🔔
               @if($hasNote)
                <hr>
                <strong>📝 ملاحظة الإدارة:</strong>
                <div class="mt-1 small">
                    {{ $club->note_admin }}
                </div>
            @endif
        </div>
    @endif

@else
    {{-- لا يوجد دوسيي بعد --}}
    <div class="alert alert-info status-box">
        ⚠ لم تقم بإرسال ملفك بعد!
        <br>
        @if($hasNote)
                <hr>
                <strong>📝 ملاحظة الإدارة:</strong>
                <div class="mt-1 small">
                    {{ $club->note_admin }}
                </div>
            @endif
        </a>
    </div>
@endif

</div>
       

        <div class="d-grid gap-2 mt-3">
            <a href="{{ route('club.dossier.index') }}"
               class="btn btn-club-outline">
               👁 عرض الملف
            </a>

            <a href="{{ route('club.dossier.edit') }}"
               class="btn btn-club-primary">
               ✏️ تعديل / إكمال الملف
            </a>
        </div>
    </div>
</div>


   
{{-- ================= 📄 تحميل النماذج ================= --}}
<div class="club-card mt-4">
    <div class="club-header">
        📄 النماذج الرسمية (دفتر الشروط)
    </div>

    <div class="p-4">
        <div class="row g-3">

            <div class="col-md-4">
                <div class="download-card">
                    <i class="bi bi-file-earmark-text"></i>
                    <h6 class="fw-bold mt-2">دفتر الشروط</h6>
                    <a href="{{ asset('docs/daftar_chorout.pdf') }}"
                       class="btn btn-club-outline btn-sm mt-2"
                       download>
                       ⬇ تحميل
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="download-card">
                    <i class="bi bi-file-earmark-check"></i>
                    <h6 class="fw-bold mt-2">نموذج الانخراط</h6>
                    <a href="{{ asset('docs/engagement.pdf') }}"
                       class="btn btn-club-outline btn-sm mt-2"
                       download>
                       ⬇ تحميل
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="download-card">
                    <i class="bi bi-file-earmark-person"></i>
                    <h6 class="fw-bold mt-2">تصريح أبوي</h6>
                    <a href="{{ asset('docs/autorisation_parentale.pdf') }}"
                       class="btn btn-club-outline btn-sm mt-2"
                       download>
                       ⬇ تحميل
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>


</div>
@endsection
