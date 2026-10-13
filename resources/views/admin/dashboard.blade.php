@extends('layouts.app')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root{
    --primary:#0b3a63;
    --primary2:#075985;
    --accent:#14b8a6;
    --bg:#f4f7fb;
    --text:#0f172a;
    --muted:#64748b;
    --border:#e5eaf2;
    --white:#fff;
}

body{
    font-family:"Cairo",sans-serif!important;
    background:var(--bg);
}

.admin-dashboard{
    direction:rtl;
    padding:24px;
    color:var(--text);
}

.dashboard-shell{
    display:grid;
    grid-template-columns:270px 1fr;
    gap:22px;
}

.sidebar{
    background:linear-gradient(180deg,#062946,#031827);
    color:#fff;
    border-radius:26px;
    padding:22px;
    min-height:calc(100vh - 48px);
    box-shadow:0 18px 40px rgba(2,23,42,.18);
    position:sticky;
    top:20px;
}

.sidebar-logo{
    font-size:24px;
    font-weight:900;
    margin-bottom:28px;
}

.sidebar-logo span{
    color:#38bdf8;
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:12px;
    color:#dbeafe;
    text-decoration:none;
    padding:13px 15px;
    border-radius:16px;
    margin-bottom:9px;
    font-weight:700;
    transition:.25s;
}

.sidebar a:hover,
.sidebar a.active{
    background:linear-gradient(135deg,#0ea5e9,#2563eb);
    color:#fff;
    transform:translateX(-4px);
}

.sidebar-footer{
    margin-top:35px;
    padding:18px;
    border:1px solid rgba(255,255,255,.15);
    border-radius:18px;
    color:#cbd5e1;
    font-size:13px;
    text-align:center;
}

.main-panel{
    min-width:0;
}

.topbar{
    background:#fff;
    border-radius:24px;
    padding:18px 22px;
    margin-bottom:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 8px 26px rgba(15,23,42,.06);
    border:1px solid var(--border);
}

.topbar h4{
    margin:0;
    font-weight:900;
    color:#082f49;
}

.user-chip{
    background:#eef6ff;
    color:#075985;
    padding:9px 14px;
    border-radius:999px;
    font-weight:800;
}

.welcome-card{
    background:
        radial-gradient(circle at top left,rgba(20,184,166,.35),transparent 35%),
        linear-gradient(135deg,#082f49,#075985);
    color:#fff;
    border-radius:28px;
    padding:28px;
    margin-bottom:20px;
    box-shadow:0 16px 38px rgba(8,47,73,.22);
    position:relative;
    overflow:hidden;
}

.welcome-card h3{
    font-weight:900;
    margin-bottom:8px;
}

.welcome-card p{
    color:#dbeafe;
    margin:0;
}

.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:16px;
    margin-bottom:20px;
}

.stat-card{
    background:#fff;
    border-radius:24px;
    padding:20px;
    box-shadow:0 10px 28px rgba(15,23,42,.07);
    border:1px solid var(--border);
    display:flex;
    align-items:center;
    gap:16px;
    position:relative;
    overflow:hidden;
}

.stat-card::after{
    content:"";
    position:absolute;
    inset:auto auto -35px -35px;
    width:90px;
    height:90px;
    border-radius:50%;
    background:rgba(14,165,233,.10);
}

.stat-icon{
    width:62px;
    height:62px;
    border-radius:20px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    color:#fff;
    flex-shrink:0;
}

.bg-blue{background:linear-gradient(135deg,#2563eb,#06b6d4);}
.bg-green{background:linear-gradient(135deg,#16a34a,#22c55e);}
.bg-orange{background:linear-gradient(135deg,#f97316,#f59e0b);}
.bg-red{background:linear-gradient(135deg,#dc2626,#fb7185);}

.stat-info .number{
    font-size:30px;
    font-weight:900;
    color:#082f49;
    line-height:1;
}

.stat-info .label{
    margin-top:7px;
    color:var(--muted);
    font-weight:800;
    font-size:14px;
}

.stat-rate{
    margin-top:10px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:#fff7ed;
    color:#c2410c;
    padding:6px 10px;
    border-radius:999px;
    font-weight:900;
    font-size:13px;
}

.stat-rate small{
    font-size:12px;
    font-weight:800;
    color:#9a3412;
}

.content-grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
    align-items:stretch;
}

.content-grid > *{
    min-width:0;
}

.panel-card{
    background:#fff;
    border-radius:26px;
    padding:clamp(16px,2vw,24px);
    box-shadow:0 10px 28px rgba(15,23,42,.07);
    border:1px solid var(--border);
    overflow:hidden;
}

.panel-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:18px;
}

.panel-title{
    font-weight:900;
    color:#082f49;
    margin:0;
}

.panel-badge{
    background:#ecfeff;
    color:#0891b2;
    padding:7px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
}

.chart-card{
    min-height:385px;
}

.chart-wrapper{
    position:relative;
    height:300px;
    width:100%;
    min-width:0;
}

.activity-row{
    display:flex;
    flex-direction:row-reverse;
    justify-content:space-between;
    align-items:center;
    padding:14px 0;
    border-bottom:1px solid #eef2f7;
}

.activity-row:last-child{
    border-bottom:0;
}

.activity-text{
    color:#334155;
    font-size:14px;
    font-weight:800;
}

.activity-badge{
    min-width:58px;
    text-align:center;
    padding:6px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
}

.activity-badge.blue{background:#e0f2fe;color:#0369a1;}
.activity-badge.green{background:#dcfce7;color:#15803d;}
.activity-badge.orange{background:#ffedd5;color:#c2410c;}
.activity-badge.purple{background:#f3e8ff;color:#7e22ce;}

.circles-row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
    margin-top:20px;
    align-items:stretch;
}

.circles-row .panel-card{
    min-height:360px;
}

.age-chart-layout{
    display:grid;
    grid-template-columns:minmax(200px,240px) 1fr;
    gap:20px;
    align-items:center;
}

.age-chart-wrapper{
    position:relative;
    width:220px;
    height:220px;
    margin:auto;
}

.age-legend{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.age-legend-item{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    background:#f8fafc;
    border:1px solid #e5eaf2;
    border-radius:14px;
    padding:10px 12px;
    font-size:13px;
    font-weight:800;
    color:#334155;
}

.age-color{
    width:14px;
    height:14px;
    border-radius:5px;
    display:inline-block;
    margin-left:6px;
}

.circle-wrapper{
    position:relative;
    width:220px;
    height:220px;
    margin:10px auto;
}

.circle-center{
    position:absolute;
    inset:0;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
}

.circle-center strong{
    font-size:34px;
    font-weight:900;
    color:#082f49;
}

.circle-center span{
    font-size:13px;
    font-weight:800;
    color:#64748b;
}

.dossier-stats{
    display:flex;
    justify-content:center;
    gap:18px;
    flex-wrap:wrap;
    margin-top:10px;
    font-size:13px;
    font-weight:800;
    color:#475569;
}

.dot{
    width:10px;
    height:10px;
    border-radius:50%;
    display:inline-block;
    margin-left:6px;
}

.dot.green{background:#16a34a;}
.dot.red{background:#ef4444;}
.dot.gray{background:#94a3b8;}
.dot.blue{background:#2563eb;}

.menu-section{
    margin-top:22px;
}

.menu-title{
    font-size:18px;
    font-weight:900;
    color:#082f49;
    margin-bottom:14px;
}

/* ===== MENU CARDS MODERNE AVEC DESCRIPTION ===== */

.menu-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
    gap:16px;
}

.dash-card{
    position:relative;
    background:linear-gradient(135deg,#ffffff,#f8fafc);
    border-radius:24px;
    padding:18px 14px 48px;
    min-height:170px;
    display:flex;
    flex-direction:column;
    align-items:flex-start;
    justify-content:flex-start;
    text-align:right;
    text-decoration:none;
    color:#0f172a;
    border:1px solid #e5eaf2;
    box-shadow:0 10px 26px rgba(15,23,42,.07);
    transition:.25s ease;
    overflow:hidden;
}

.dash-card::before{
    content:"";
    position:absolute;
    top:-35px;
    left:-35px;
    width:95px;
    height:95px;
    border-radius:50%;
    background:rgba(14,165,233,.10);
    transition:.25s ease;
}

.dash-card::after{
    content:"";
    position:absolute;
    right:0;
    top:0;
    width:4px;
    height:100%;
    background:linear-gradient(180deg,#0ea5e9,#14b8a6);
    opacity:.85;
}

.dash-card:hover{
    transform:translateY(-6px);
    box-shadow:0 18px 36px rgba(15,23,42,.14);
    border-color:#38bdf8;
    color:#075985;
}

.dash-card:hover::before{
    transform:scale(1.15);
    background:rgba(14,165,233,.16);
}

.dash-icon{
    width:52px;
    height:52px;
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:25px;
    background:linear-gradient(135deg,#e0f2fe,#f0fdfa);
    margin-bottom:12px;
    box-shadow:inset 0 0 0 1px rgba(255,255,255,.7);
    z-index:1;
}

.dash-card h6{
    font-size:15px;
    font-weight:900;
    margin:0 0 7px;
    color:#082f49;
    z-index:1;
}

.dash-desc{
    font-size:12.5px;
    line-height:1.7;
    color:#64748b;
    font-weight:700;
    margin:0;
    padding-left:34px;
    z-index:1;
}

.count-box{
    position:absolute;
    left:14px;
    bottom:14px;
    min-width:38px;
    height:26px;
    padding:3px 11px;
    border-radius:999px;
    background:#eef6ff;
    color:#075985;
    font-size:12px;
    font-weight:900;
    display:flex;
    align-items:center;
    justify-content:center;
    border:1px solid #dbeafe;
    z-index:2;
}

/* Carte danger : توقيف المنشأة */
.danger-card{
    background:linear-gradient(135deg,#fff7ed,#ffffff);
}

.danger-card::after{
    background:linear-gradient(180deg,#ef4444,#f97316);
}

.danger-card .dash-icon{
    background:linear-gradient(135deg,#fee2e2,#ffedd5);
    color:#dc2626;
}

.danger-card:hover{
    border-color:#fb7185;
}

.danger-card .count-box{
    background:#fee2e2;
    color:#b91c1c;
    border-color:#fecaca;
}

/* ===== RESPONSIVE ===== */

@media(max-width:1100px){
    .dashboard-shell{
        grid-template-columns:1fr;
    }

    .sidebar{
        position:relative;
        min-height:auto;
    }

    .stats-grid{
        grid-template-columns:repeat(2,1fr);
    }

    .content-grid{
        grid-template-columns:1fr;
    }

    .circles-row{
        grid-template-columns:1fr;
    }

    .menu-grid{
        grid-template-columns:repeat(3,1fr);
    }
}

@media(max-width:768px){
    .admin-dashboard{
        padding:14px;
    }

    .topbar{
        flex-direction:column;
        gap:12px;
        align-items:flex-start;
    }

    .stats-grid{
        grid-template-columns:1fr;
    }

    .menu-grid{
        grid-template-columns:repeat(2,1fr);
        gap:12px;
    }

    .dash-card{
        min-height:160px;
        padding:16px 12px 46px;
        border-radius:20px;
    }

    .dash-icon{
        width:46px;
        height:46px;
        font-size:22px;
    }

    .dash-card h6{
        font-size:14px;
    }

    .dash-desc{
        font-size:12px;
        padding-left:28px;
    }

    .age-chart-layout{
        grid-template-columns:1fr;
    }

    .chart-card{
        min-height:340px;
    }

    .chart-wrapper{
        height:255px;
    }
}

@media(max-width:420px){
    .menu-grid{
        grid-template-columns:1fr;
    }

    .chart-wrapper{
        height:230px;
    }

    .dash-card{
        min-height:auto;
        padding:15px 14px 48px;
    }

    .count-box{
        bottom:13px;
    }
}
</style>

<div class="admin-dashboard">
    <div class="dashboard-shell">

        <aside class="sidebar">
            <div class="sidebar-logo">OPOW <span>Dashboard</span></div>

            <a href="#" class="active">🏠 الرئيسية</a>
            <a href="{{ route('admins.index') }}">👑️ المسؤولون</a>
            
            <a href="{{ route('admin.dossiers.index') }}">🗂️ الملفات</a>
            <a href="{{ route('persons.index') }}">👥 المنخرطون</a>
            <a href="{{ route('admin.complexes.index') }}">🏟️ المنشآت</a>
            <a href="{{ route('events.index') }}">📅 الفعاليات</a>
            
                 <a href="{{ route('admin.schedules.index') }}">⏰ الأفواج والتسعيرة</a>
            <a href="{{ route('tickets.index') }}">🎫 التذاكر</a>
            <a href="{{ route('matches.index') }}">⚽ المباريات</a>
            <a href="{{ route('news.index') }}">📰 الأخبار</a>

         
        </aside>

        <main class="main-panel">

            <div class="topbar">
                <h4>لوحة التحكم</h4>
                <div class="user-chip">👤 {{ Auth::user()->name }}</div>
            </div>

            <div class="welcome-card">
                <h3>مرحباً {{ Auth::user()->name }}</h3>
                <p>إدارة الملفات، المنشآت، التسجيلات، الحجوزات، التذاكر والإحصائيات من مكان واحد.</p>
         
            
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bg-blue">👥</div>
                    <div class="stat-info">
                        <div class="number">{{$totalAgeRegistrations ?? 0 }}</div>
                        <div class="label">إجمالي المنخرطين</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-green">🗂️</div>
                    <div class="stat-info">
                        <div class="number">{{ $dossiersCount ?? 0 }}</div>
                        <div class="label">إجمالي الملفات</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-orange">📅</div>
                    <div class="stat-info">
                        <div class="number">{{ $reservationsCount ?? \App\Models\Reservation::count() }}</div>
                        <div class="label">إجمالي الحجوزات</div>

                        <div class="stat-rate">
                            <span>{{ $reservationRate ?? 0 }}%</span>
                            <small>مقارنة بالمنخرطين</small>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-red">🎫</div>
                    <div class="stat-info">
                        <div class="number">{{ $ticketsCount ?? \App\Models\Ticket::count() }}</div>
                        <div class="label">إجمالي التذاكر</div>
                    </div>
                </div>
            </div>

            <div class="content-grid">

                <div class="panel-card chart-card">
                    <div class="panel-head">
                        <h5 class="panel-title">إحصائيات الحجوزات حسب الأشهر</h5>
                        <span class="panel-badge">12 شهر</span>
                    </div>

                    <div class="chart-wrapper">
                        <canvas id="reservationsChart"></canvas>
                    </div>
                </div>

                <div class="panel-card">
                    <div class="panel-head">
                        <h5 class="panel-title">النشاطات الأخيرة</h5>
                        <span class="panel-badge">اليوم</span>
                    </div>

                    <div class="activity-row">
                        <span class="activity-text">ملف جديد قيد الدراسة</span>
                        <span class="activity-badge blue">{{ $recentDossiersCount ?? 0 }}</span>
                    </div>

                    <div class="activity-row">
                        <span class="activity-text">حجز جديد في المنشآت</span>
                        <span class="activity-badge green">{{ $recentReservationsCount ?? 0 }}</span>
                    </div>

                    <div class="activity-row">
                        <span class="activity-text">تذكرة جديدة</span>
                        <span class="activity-badge orange">{{ $recentTicketsCount ?? 0 }}</span>
                    </div>

                    <div class="activity-row">
                        <span class="activity-text">فعالية مضافة</span>
                        <span class="activity-badge purple">{{ $recentEventsCount ?? 0 }}</span>
                    </div>
                </div>

            </div>

            <div class="circles-row">

                <div class="panel-card age-chart-card">
                    <div class="panel-head">
                        <h5 class="panel-title">نسبة التسجيل حسب الفئات العمرية</h5>
                        <span class="panel-badge">{{ $totalAgeRegistrations ?? 0 }} تسجيل</span>
                    </div>

                    <div class="age-chart-layout">
                        <div class="age-chart-wrapper">
                            <canvas id="ageCategoriesCircleChart"></canvas>
                        </div>

                        <div class="age-legend" id="ageLegend"></div>
                    </div>
                </div>

                <div class="panel-card dossier-circle-card">
                    <div class="panel-head">
                        <h5 class="panel-title">نسبة معالجة الملفات</h5>
                        <span class="panel-badge">{{ $dossierProcessingPercent ?? 0 }}%</span>
                    </div>

                    <div class="circle-wrapper">
                        <canvas id="dossierCircleChart"></canvas>
                        <div class="circle-center">
                            <strong>{{ $dossierProcessingPercent ?? 0 }}%</strong>
                            <span>معالجة</span>
                        </div>
                    </div>

                    <div class="dossier-stats">
                        <div><span class="dot green"></span> مقبولة: {{ $approvedDossiersCount ?? 0 }}</div>
                        <div><span class="dot red"></span> مرفوضة: {{ $rejectedDossiersCount ?? 0 }}</div>
                        <div><span class="dot gray"></span> الإجمالي: {{ $dossiersCount ?? 0 }}</div>
                    </div>
                </div>

            </div>

            {{-- ===== إحصائيات حسب المنشآت ===== --}}
            <div class="menu-section">
                <h5 class="menu-title">📊 إحصائيات حسب المنشآت</h5>

                <div class="content-grid">
                    <div class="panel-card chart-card">
                        <div class="panel-head">
                            <h5 class="panel-title">عدد الحجوزات حسب المنشأة</h5>
                            <span class="panel-badge">{{ $complexStats->count() }} منشأة</span>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="complexReservationsChart"></canvas>
                        </div>
                    </div>

                    <div class="panel-card chart-card">
                        <div class="panel-head">
                            <h5 class="panel-title">نسبة المنخرطين حسب الجنس</h5>
                            <span class="panel-badge">{{ $totalAgeRegistrations ?? 0 }} منخرط</span>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="genderChart"></canvas>
                        </div>
                        <div class="age-legend" id="genderLegend"></div>
                    </div>
                </div>

                <div class="panel-card" style="margin-top:20px;">
                    <div class="panel-head">
                        <h5 class="panel-title">تفاصيل المنشآت</h5>
                        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                            <span class="panel-badge">{{ $complexStats->count() }} منشأة</span>
                            <a href="{{ route('admin.complexes.print') }}" target="_blank" class="btn btn-sm btn-light" style="font-weight:800;">🖨️ طباعة</a>
                            <a href="{{ route('admin.complexes.print') }}" target="_blank" class="btn btn-sm btn-primary" style="font-weight:800;">📄 PDF</a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:14px;font-weight:700;">
                            <thead class="table-light" style="color:#082f49;">
                                <tr>
                                    <th>المنشأة</th>
                                    <th>المنخرطون</th>
                                    <th>الحجوزات</th>
                                    <th>ملفات مقبولة</th>
                                    <th>المبالغ المدفوعة (دج)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($complexStats as $cs)
                                <tr>
                                    <td>{{ $cs->nom }}</td>
                                    <td>{{ number_format($cs->subscribers) }}</td>
                                    <td>{{ number_format($cs->reservations) }}</td>
                                    <td>{{ number_format($cs->approved) }}</td>
                                    <td>{{ number_format($cs->paidAmount, 0, ',', ' ') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">لا توجد منشآت</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="menu-section">
                <h5 class="menu-title">القائمة السريعة</h5>

              <div class="menu-grid">
    <a href="{{ route('news.index') }}" class="dash-card">
        <div class="dash-icon">📰</div>
        <h6>الأخبار</h6>
        <p class="dash-desc">إدارة ونشر أخبار المؤسسة الرياضية.</p>
        <div class="count-box">{{ \App\Models\News::count() }}</div>
    </a>

    <a href="{{ route('events.index') }}" class="dash-card">
        <div class="dash-icon">📅</div>
        <h6>الأحداث</h6>
        <p class="dash-desc">تنظيم ومتابعة الأحداث والتظاهرات.</p>
        <div class="count-box">{{ \App\Models\Event::count() }}</div>
    </a>

    <a href="{{ route('admin.dossiers.index') }}" class="dash-card">
        <div class="dash-icon">🗂️</div>
        <h6>دراسة الملفات</h6>
        <p class="dash-desc">متابعة ملفات التسجيل والموافقة عليها.</p>
        <div class="count-box">{{ $dossiersCount ?? 0 }}</div>
    </a>

    <a href="{{ route('admin.clubs.index') }}" class="dash-card">
        <div class="dash-icon">🏊‍♂️</div>
        <h6>النوادي الرياضية</h6>
        <p class="dash-desc">إدارة النوادي والفرق الرياضية.</p>
        <div class="count-box">{{ $clubsCount ?? 0 }}</div>
    </a>

    <a href="{{ route('admin.companies.index') }}" class="dash-card">
        <div class="dash-icon">🏢</div>
        <h6>المؤسسات</h6>
        <p class="dash-desc">إدارة حسابات المؤسسات المسجلة.</p>
        <div class="count-box">{{ $companiesCount ?? 0 }}</div>
    </a>

    <a href="{{ route('persons.index') }}" class="dash-card">
        <div class="dash-icon">👥</div>
        <h6>المنخرطون</h6>
        <p class="dash-desc">عرض وإدارة بيانات المنخرطين.</p>
        <div class="count-box">{{ $totalAgeRegistrations ?? 0 }}</div>
    </a>

    <a href="{{ route('admin.cards') }}" class="dash-card" target="_blank">
        <div class="dash-icon">🪪</div>
        <h6>بطاقات اللاعبين</h6>
        <p class="dash-desc">طباعة بطاقات منخرطي النوادي والمؤسسات والفئات الفردية.</p>
        <div class="count-box">🖨️</div>
    </a>

    <a href="{{ route('admin.activities.index') }}" class="dash-card">
        <div class="dash-icon">🏋️‍♂️</div>
        <h6>التخصصات الرياضية</h6>
        <p class="dash-desc">ضبط الأنشطة والتخصصات المتاحة.</p>
        <div class="count-box">{{ \App\Models\Activity::count() }}</div>
    </a>

    <a href="{{ route('admin.complexes.index') }}" class="dash-card">
        <div class="dash-icon">🏟️</div>
        <h6>المنشآت الرياضية</h6>
        <p class="dash-desc">إدارة القاعات والمسابح والمرافق.</p>
        <div class="count-box">{{ \App\Models\Complex::count() }}</div>
    </a>

    <a href="{{ route('admin.schedules.index') }}" class="dash-card">
        <div class="dash-icon">⏰</div>
        <h6>الأفواج والتسعيرة</h6>
        <p class="dash-desc">تحديد الأفواج، التوقيت والأسعار.</p>
        <div class="count-box">{{ \App\Models\Schedule::count() }}</div>
    </a>

    <a href="{{ route('reservations.index') }}" class="dash-card">
        <div class="dash-icon">📝</div>
        <h6>توزيع الأفواج</h6>
        <p class="dash-desc">متابعة توزيع المنخرطين على الأفواج.</p>
        <div class="count-box">{{ \App\Models\Reservation::count() }}</div>
    </a>

    <a href="{{ route('seasons.index') }}" class="dash-card">
        <div class="dash-icon">🗓️</div>
        <h6>رزنامة التسجيلات</h6>
        <p class="dash-desc">ضبط فترات فتح وغلق التسجيلات.</p>
        <div class="count-box">{{ \App\Models\Season::count() }}</div>
    </a>

    <a href="{{ route('matches.index') }}" class="dash-card">
        <div class="dash-icon">⚽</div>
        <h6>المباريات</h6>
        <p class="dash-desc">إدارة المباريات والبرمجة الرياضية.</p>
        <div class="count-box">{{ \App\Models\MatchModel::count() }}</div>
    </a>

    <a href="{{ route('tickets.index') }}" class="dash-card">
        <div class="dash-icon">🎫</div>
        <h6>التذاكر</h6>
        <p class="dash-desc">متابعة التذاكر وطلبات الدخول.</p>
        <div class="count-box">{{ \App\Models\Ticket::count() }}</div>
    </a>

    <a href="{{ route('teams.index') }}" class="dash-card">
        <div class="dash-icon">🤼‍♂️</div>
        <h6>الفرق</h6>
        <p class="dash-desc">إدارة الفرق والأصناف الرياضية.</p>
        <div class="count-box">{{ \App\Models\Team::count() }}</div>
    </a>

    <a href="{{ route('age-categories.index') }}" class="dash-card">
        <div class="dash-icon">👶</div>
        <h6>فئات العمر</h6>
        <p class="dash-desc">ضبط الفئات العمرية للأصناف.</p>
        <div class="count-box">{{ \App\Models\AgeCategory::count() }}</div>
    </a>

    <a href="{{ route('admin.capacities.index') }}" class="dash-card">
        <div class="dash-icon">📊</div>
        <h6>السعات والطاقات</h6>
        <p class="dash-desc">ضبط طاقة النشاطات في المنشآت.</p>
        <div class="count-box">{{ \App\Models\ComplexActivity::count() }}</div>
    </a>

    <a href="{{ route('admin.assurances.index') }}" class="dash-card">
        <div class="dash-icon">🛡️</div>
        <h6>التأمينات</h6>
        <p class="dash-desc">متابعة التأمين السنوي للمنخرطين.</p>
        <div class="count-box">{{ \App\Models\Person::where('etat_ass', 1)->count() }}</div>
    </a>

    <a href="{{ route('seat_types.index') }}" class="dash-card">
        <div class="dash-icon">🪑</div>
        <h6>أنواع المقاعد</h6>
        <p class="dash-desc">ضبط أنواع المقاعد وأسعارها.</p>
        <div class="count-box">{{ \App\Models\SeatType::count() }}</div>
    </a>

    <a href="{{ route('complex_seats.index') }}" class="dash-card">
        <div class="dash-icon">🏟️</div>
        <h6>مقاعد المنشآت</h6>
        <p class="dash-desc">توزيع المقاعد على المنشآت والمباريات.</p>
        <div class="count-box">{{ \App\Models\ComplexSeat::count() }}</div>
    </a>

    <a href="{{ route('admin.accounts.no-dossier') }}" class="dash-card danger-card">
        <div class="dash-icon">🚫</div>
        <h6>حسابات بدون ملف</h6>
        <p class="dash-desc">حذف الحسابات التي لم تقدم أي ملف — فردي أو جماعي.</p>
        <div class="count-box">{{ $noDossierAccountsCount ?? 0 }}</div>
    </a>

    </div>
            </div>

        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const reservationsCtx = document.getElementById('reservationsChart');
    const reservationsData = @json(array_values($chartReservations ?? array_fill(0, 12, 0)));

    if (reservationsCtx) {
        new Chart(reservationsCtx, {
            type: 'bar',
            data: {
                labels: [
                    'جانفي', 'فيفري', 'مارس', 'أفريل',
                    'ماي', 'جوان', 'جويلية', 'أوت',
                    'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
                ],
                datasets: [{
                    label: 'عدد الحجوزات',
                    data: reservationsData,
                    backgroundColor: [
                        '#2563eb', '#16a34a', '#f97316', '#dc2626',
                        '#7c3aed', '#0891b2', '#ca8a04', '#db2777',
                        '#059669', '#4f46e5', '#ea580c', '#64748b'
                    ],
                    borderColor: [
                        '#1d4ed8', '#15803d', '#ea580c', '#b91c1c',
                        '#6d28d9', '#0e7490', '#a16207', '#be185d',
                        '#047857', '#4338ca', '#c2410c', '#475569'
                    ],
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 18,
                    maxBarThickness: 22,
                    categoryPercentage: 0.55,
                    barPercentage: 0.65
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        rtl: true,
                        textDirection: 'rtl',
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { family: 'Cairo', size: 13, weight: '700' },
                        bodyFont: { family: 'Cairo', size: 13 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#475569',
                            font: { family: 'Cairo', size: 11, weight: '700' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grace: '15%',
                        grid: { color: '#e5e7eb' },
                        ticks: {
                            color: '#64748b',
                            precision: 0,
                            stepSize: 1,
                            font: { family: 'Cairo', size: 11 }
                        }
                    }
                }
            }
        });
    }

    const ageCtx = document.getElementById('ageCategoriesCircleChart');

    if (ageCtx) {
        const ageLabels = @json($ageCategoryLabels ?? []);
        const ageValues = @json($ageCategoryValues ?? []);
        const ageColors = [
            '#2563eb', '#16a34a', '#f97316',
            '#dc2626', '#7c3aed', '#0891b2',
            '#ca8a04', '#db2777'
        ];

        const total = ageValues.reduce((a, b) => a + Number(b), 0);

        new Chart(ageCtx, {
            type: 'doughnut',
            data: {
                labels: ageLabels,
                datasets: [{
                    data: ageValues,
                    backgroundColor: ageColors,
                    borderColor: '#ffffff',
                    borderWidth: 4,
                    cutout: '72%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        rtl: true,
                        textDirection: 'rtl',
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                const value = Number(context.raw);
                                const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                                return context.label + ': ' + value + ' تسجيل (' + percent + '%)';
                            }
                        }
                    }
                }
            }
        });

        const legendBox = document.getElementById('ageLegend');

        if (legendBox) {
            legendBox.innerHTML = ageLabels.map((label, index) => {
                const value = Number(ageValues[index] || 0);
                const percent = total > 0 ? Math.round((value / total) * 100) : 0;

                return `
                    <div class="age-legend-item">
                        <div>
                            <span class="age-color" style="background:${ageColors[index % ageColors.length]}"></span>
                            ${label}
                        </div>
                        <strong>${value} / ${percent}%</strong>
                    </div>
                `;
            }).join('');
        }
    }

 const dossierCtx = document.getElementById('dossierCircleChart');

if (dossierCtx) {
    const approved = {{ $approvedDossiersCount ?? 0 }};
    const rejected = {{ $rejectedDossiersCount ?? 0 }};
    const pending  = {{ $pendingDossiersCount ?? 0 }};

    new Chart(dossierCtx, {
        type: 'doughnut',
        data: {
            labels: ['مقبولة', 'مرفوضة', 'قيد المعالجة'],
            datasets: [{
                data: [approved, rejected, pending],
                backgroundColor: ['#16a34a', '#ef4444', '#cbd5e1'],
                borderColor: '#ffffff',
                borderWidth: 4,
                cutout: '76%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    rtl: true,
                    textDirection: 'rtl',
                    backgroundColor: '#0f172a',
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: function(context) {
                            const total = approved + rejected + pending;
                            const value = Number(context.raw);
                            const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                            return context.label + ': ' + value + ' ملف (' + percent + '%)';
                        }
                    }
            }
        }
    }
    });
}

// 📊 Chart: حجوزات حسب المنشأة
const complexResCtx = document.getElementById('complexReservationsChart');
if (complexResCtx) {
    const labels = @json($complexLabels ?? []);
    const values = @json($complexReservations ?? []);

    new Chart(complexResCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'عدد الحجوزات',
                data: values,
                backgroundColor: 'rgba(14,165,233,.75)',
                borderColor: '#0ea5e9',
                borderWidth: 1,
                borderRadius: 8,
                barThickness: 22,
                maxBarThickness: 26
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    rtl: true,
                    textDirection: 'rtl',
                    backgroundColor: '#0f172a',
                    padding: 12,
                    cornerRadius: 12,
                    titleFont: { family: 'Cairo', size: 12, weight: '700' },
                    bodyFont: { family: 'Cairo', size: 12 }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#64748b', font: { family: 'Cairo', size: 11 } },
                    grid: { color: '#e5e7eb' }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#334155', font: { family: 'Cairo', size: 11, weight: '700' } }
                }
            }
        }
    });
}

// 👫 Chart: نسبة المنخرطين حسب الجنس
const genderCtx = document.getElementById('genderChart');
if (genderCtx) {
    const genderData = @json($genderGlobal ?? ['ذكر' => 0, 'أنثى' => 0, 'غير محدد' => 0]);
    const values = [genderData['ذكر'] || 0, genderData['أنثى'] || 0, genderData['غير محدد'] || 0];
    const total = values.reduce((a, b) => a + Number(b), 0);

    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: ['ذكور', 'إناث', 'غير محدد'],
            datasets: [{
                data: values,
                backgroundColor: ['#2563eb', '#f472b6', '#cbd5e1'],
                borderColor: '#ffffff',
                borderWidth: 4,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    rtl: true,
                    textDirection: 'rtl',
                    backgroundColor: '#0f172a',
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: function(context) {
                            const value = Number(context.raw);
                            const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                            return context.label + ': ' + value + ' (' + percent + '%)';
                        }
                    }
                }
            }
        }
    });

    const genderLegend = document.getElementById('genderLegend');
    if (genderLegend) {
        const gLabels = ['ذكور', 'إناث', 'غير محدد'];
        const gColors = ['#2563eb', '#f472b6', '#cbd5e1'];
        genderLegend.innerHTML = gLabels.map((label, i) => {
            const value = Number(values[i] || 0);
            const percent = total > 0 ? Math.round((value / total) * 100) : 0;
            return `
                <div class="age-legend-item">
                    <div>
                        <span class="age-color" style="background:${gColors[i]}"></span>
                        ${label}
                    </div>
                    <strong>${value} / ${percent}%</strong>
                </div>
            `;
        }).join('');
    }
}

});
</script>

@endsection