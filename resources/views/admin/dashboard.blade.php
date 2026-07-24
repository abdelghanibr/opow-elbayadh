@extends('layouts.app')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
    --dash-blue: #0b78a8;
    --dash-blue-dark: #075985;
    --dash-cyan: #18a8c7;
    --dash-green: #00a86b;
    --dash-red: #e74c3c;
    --dash-orange: #f39c12;
    --dash-navy: #0b2545;
    --dash-purple: #6d5dfc;
    --dash-bg: #eef3f7;
    --dash-card: #ffffff;
    --dash-text: #1f2937;
    --dash-muted: #64748b;
    --dash-border: #dbe5ec;
    --dash-shadow: 0 6px 18px rgba(15, 23, 42, .10);
}

body {
    font-family: "Cairo", sans-serif !important;
    background: var(--dash-bg);
}

.dashboard-tv {
    direction: rtl;
    background: var(--dash-bg);
    min-height: 100vh;
    color: var(--dash-text);
    display: grid;
    grid-template-columns: 58px 1fr;
}

/* Sidebar verticale */
.tv-sidebar {
    background: #1f2f3a;
    min-height: 100vh;
    padding: 10px 7px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    position: sticky;
    top: 0;
    z-index: 20;
}

.tv-brand-mini {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: var(--dash-blue);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 13px;
    margin-bottom: 8px;
}

.tv-side-link {
    width: 42px;
    height: 42px;
    border-radius: 9px;
    color: rgba(255,255,255,.78);
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 18px;
    transition: .22s ease;
}

.tv-side-link:hover,
.tv-side-link.active {
    background: var(--dash-blue);
    color: #fff;
    transform: translateX(-2px);
}

/* Main */
.tv-main {
    padding: 10px;
    min-width: 0;
}

/* Topbar */
.tv-topbar {
    height: 38px;
    background: var(--dash-blue);
    color: #fff;
    border-radius: 3px;
    margin-bottom: 10px;
    padding: 0 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.tv-topbar-title {
    font-size: 15px;
    font-weight: 900;
}

.tv-user {
    font-size: 12px;
    font-weight: 800;
    background: rgba(255,255,255,.18);
    border-radius: 999px;
    padding: 5px 10px;
}

/* KPI row */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 8px;
}

.kpi-card {
    min-height: 86px;
    color: #fff;
    border-radius: 3px;
    padding: 10px;
    position: relative;
    overflow: hidden;
    box-shadow: var(--dash-shadow);
}

.kpi-card::after {
    content: "";
    position: absolute;
    width: 80px;
    height: 80px;
    left: -18px;
    bottom: -20px;
    background: rgba(255,255,255,.12);
    border-radius: 50%;
}

.kpi-card .kpi-label {
    font-size: 12px;
    font-weight: 800;
    opacity: .94;
    line-height: 1.5;
}

.kpi-card .kpi-value {
    margin-top: 8px;
    font-size: 27px;
    font-weight: 900;
    line-height: 1;
}

.kpi-card .kpi-sub {
    font-size: 11px;
    margin-top: 6px;
    opacity: .9;
}

.kpi-icon-bg {
    position: absolute;
    left: 12px;
    top: 18px;
    font-size: 42px;
    opacity: .18;
}

.kpi-blue { background: #18a8c7; }
.kpi-green { background: #00a86b; }
.kpi-red { background: #e74c3c; }
.kpi-orange { background: #f39c12; }
.kpi-navy { background: #0b2545; }
.kpi-sky { background: #1f9ed4; }

/* Layout like image */
.tv-content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 8px;
}

.tv-panel {
    background: var(--dash-card);
    border: 1px solid var(--dash-border);
    border-radius: 3px;
    box-shadow: var(--dash-shadow);
    padding: 10px;
    min-width: 0;
}

.tv-panel-title {
    font-size: 13px;
    font-weight: 900;
    color: var(--dash-blue-dark);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.tv-panel-title span {
    background: #e7f6fb;
    color: var(--dash-blue-dark);
    border-radius: 999px;
    padding: 3px 8px;
    font-size: 10px;
    font-weight: 900;
}

.chart-large {
    height: 280px;
    position: relative;
}

.donut-box {
    height: 280px;
    position: relative;
}

/* lower grid */
.lower-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 2fr;
    gap: 8px;
    margin-top: 8px;
}

.mini-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}

.small-stat {
    min-height: 86px;
    color: #fff;
    border-radius: 3px;
    padding: 10px;
    position: relative;
    overflow: hidden;
    box-shadow: var(--dash-shadow);
}

.small-stat .value {
    font-size: 26px;
    font-weight: 900;
    line-height: 1;
}

.small-stat .label {
    font-size: 12px;
    font-weight: 800;
    margin-top: 8px;
}

.small-stat .sub {
    font-size: 10px;
    opacity: .86;
    margin-top: 4px;
}

.small-stat::after {
    content: "";
    position: absolute;
    width: 70px;
    height: 70px;
    left: -20px;
    bottom: -22px;
    background: rgba(255,255,255,.13);
    border-radius: 50%;
}

/* Quick menu */
.quick-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-top: 8px;
}

.quick-card {
    background: #fff;
    border: 1px solid var(--dash-border);
    border-radius: 3px;
    padding: 12px 8px;
    min-height: 82px;
    text-align: center;
    color: var(--dash-text);
    text-decoration: none;
    box-shadow: var(--dash-shadow);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: .2s ease;
}

.quick-card:hover {
    transform: translateY(-3px);
    color: var(--dash-blue-dark);
}

.quick-icon {
    font-size: 22px;
}

.quick-title {
    font-size: 12px;
    font-weight: 900;
}

.quick-count {
    min-width: 28px;
    height: 20px;
    border-radius: 999px;
    background: #e7f6fb;
    color: var(--dash-blue-dark);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 900;
}

/* Activity list */
.activity-list {
    display: grid;
    gap: 8px;
}

.activity-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #edf2f7;
    padding: 9px 10px;
    border-radius: 3px;
}

.activity-line strong {
    font-size: 12px;
    color: #334155;
}

.activity-line span {
    min-width: 30px;
    height: 24px;
    border-radius: 999px;
    color: #fff;
    font-size: 12px;
    font-weight: 900;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.badge-blue { background: var(--dash-blue); }
.badge-green { background: var(--dash-green); }
.badge-orange { background: var(--dash-orange); }
.badge-red { background: var(--dash-red); }

/* responsive */
@media (max-width: 1200px) {
    .kpi-grid {
        grid-template-columns: repeat(4, 1fr);
    }

    .tv-content-grid {
        grid-template-columns: 1fr;
    }

    .lower-grid {
        grid-template-columns: 1fr 1fr;
    }

    .quick-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-tv {
        grid-template-columns: 1fr;
    }

    .tv-sidebar {
        min-height: auto;
        position: relative;
        flex-direction: row;
        overflow-x: auto;
        justify-content: flex-start;
    }

    .tv-brand-mini {
        min-width: 38px;
        margin-bottom: 0;
    }

    .tv-side-link {
        min-width: 42px;
    }

    .kpi-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .lower-grid {
        grid-template-columns: 1fr;
    }

    .quick-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .chart-large,
    .donut-box {
        height: 240px;
    }
}

@media (max-width: 480px) {
    .kpi-grid {
        grid-template-columns: 1fr;
    }

    .quick-grid {
        grid-template-columns: 1fr;
    }

    .tv-main {
        padding: 8px;
    }
}
</style>

<div class="dashboard-tv">

    {{-- Sidebar compacte --}}
<aside class="tv-sidebar">
    <div class="tv-brand-mini">MY</div>

    <a href="#" class="tv-side-link active" title="الرئيسية">🏠</a>

    <a href="{{ route('admins.index') }}" class="tv-side-link" title="المسؤولون">👑</a>

    <a href="{{ route('persons.index') }}" class="tv-side-link" title="المنخرطون">👥</a>

    <a href="{{ route('admin.complexes.index') }}" class="tv-side-link" title="المنشآت">🏟️</a>

    <a href="{{ route('admin.schedules.index') }}" class="tv-side-link" title="الأفواج">⏰</a>

    <a href="{{ route('reservations.index') }}" class="tv-side-link" title="الحجوزات">📝</a>

    <a href="{{ route('tickets.index') }}" class="tv-side-link" title="التذاكر">🎫</a>

    <a href="{{ route('matches.index') }}" class="tv-side-link" title="المباريات">⚽</a>

    <a href="{{ route('teams.index') }}" class="tv-side-link" title="الفرق">🏆</a>

    <a href="{{ route('news.index') }}" class="tv-side-link" title="الأخبار">📰</a>

    <a href="{{ route('events.index') }}" class="tv-side-link" title="الفعاليات">📅</a>

    <a href="{{ route('admin.pool-closures.index') }}" class="tv-side-link" title="توقيف المنشأة">⚠️</a>
</aside>
    <main class="tv-main">

        {{-- Topbar --}}
        <div class="tv-topbar">
            <div class="tv-topbar-title">
                لوحة التحكم | {{ config('app.name') }}
            </div>

            <div class="tv-user">
                👤 {{ Auth::user()->name }}
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="kpi-grid">

            <div class="kpi-card kpi-blue">
                <div class="kpi-icon-bg">👥</div>
                <div class="kpi-label">إجمالي المنخرطين</div>
                <div class="kpi-value">{{ $totalAgeRegistrations ?? 0 }}</div>
                <div class="kpi-sub">Total Registrations</div>
            </div>

            <div class="kpi-card kpi-green">
                <div class="kpi-icon-bg">🗂️</div>
                <div class="kpi-label">إجمالي الملفات</div>
                <div class="kpi-value">{{ $dossiersCount ?? 0 }}</div>
                <div class="kpi-sub">Dossiers</div>
            </div>

            <div class="kpi-card kpi-red">
                <div class="kpi-icon-bg">📈</div>
                <div class="kpi-label">نسبة الحجز</div>
                <div class="kpi-value">{{ $reservationRate ?? 0 }}%</div>
                <div class="kpi-sub">Reservations Rate</div>
            </div>

            <div class="kpi-card kpi-orange">
                <div class="kpi-icon-bg">📅</div>
                <div class="kpi-label">إجمالي الحجوزات</div>
                <div class="kpi-value">{{ $reservationsCount ?? \App\Models\Reservation::count() }}</div>
                <div class="kpi-sub">Reservations</div>
            </div>

            <div class="kpi-card kpi-navy">
                <div class="kpi-icon-bg">🎫</div>
                <div class="kpi-label">إجمالي التذاكر</div>
                <div class="kpi-value">{{ $ticketsCount ?? \App\Models\Ticket::count() }}</div>
                <div class="kpi-sub">Tickets</div>
            </div>

            <div class="kpi-card kpi-sky">
                <div class="kpi-icon-bg">🏆</div>
                <div class="kpi-label">الفرق</div>
                <div class="kpi-value">{{ $teamsCount ?? 0 }}</div>
                <div class="kpi-sub">Teams</div>
            </div>

            <div class="kpi-card kpi-sky">
                <div class="kpi-icon-bg">⚽</div>
                <div class="kpi-label">المباريات</div>
                <div class="kpi-value">{{ \App\Models\MatchModel::count() }}</div>
                <div class="kpi-sub">Matches</div>
            </div>

        </div>

        {{-- Main charts --}}
        <div class="tv-content-grid">

            <div class="tv-panel">
                <div class="tv-panel-title">
                    إحصائيات الحجوزات حسب الأشهر
                    <span>12 شهر</span>
                </div>
                <div class="chart-large">
                    <canvas id="reservationsChart"></canvas>
                </div>
            </div>

            <div class="tv-panel">
                <div class="tv-panel-title">
                    الفئات العمرية
                    <span>{{ $totalAgeRegistrations ?? 0 }}</span>
                </div>
                <div class="donut-box">
                    <canvas id="ageCategoriesCircleChart"></canvas>
                </div>
            </div>

            <div class="tv-panel">
                <div class="tv-panel-title">
                    معالجة الملفات
                    <span>{{ $dossierProcessingPercent ?? 0 }}%</span>
                </div>
                <div class="donut-box">
                    <canvas id="dossierCircleChart"></canvas>
                </div>
            </div>

        </div>

        {{-- Lower blocks --}}
        <div class="lower-grid">

            <div class="mini-grid">
                <div class="small-stat kpi-blue">
                    <div class="value">{{ \App\Models\Complex::count() }}</div>
                    <div class="label">المنشآت</div>
                    <div class="sub">Facilities</div>
                </div>

                <div class="small-stat kpi-sky">
                    <div class="value">{{ \App\Models\Activity::count() }}</div>
                    <div class="label">التخصصات الرياضية</div>
                    <div class="sub">Activities</div>
                </div>
            </div>

            <div class="mini-grid">
                <div class="small-stat kpi-green">
                    <div class="value">{{ \App\Models\Schedule::count() }}</div>
                    <div class="label">الأفواج والتسعيرة</div>
                    <div class="sub">Schedules</div>
                </div>

                <div class="small-stat kpi-navy">
                    <div class="value">{{ \App\Models\Season::count() }}</div>
                    <div class="label">رزنامة التسجيلات</div>
                    <div class="sub">Seasons</div>
                </div>
            </div>

            <div class="tv-panel">
                <div class="tv-panel-title">
                    النشاطات الأخيرة
                    <span>اليوم</span>
                </div>

                <div class="activity-list">
                    <div class="activity-line">
                        <strong>ملف جديد قيد الدراسة</strong>
                        <span class="badge-blue">{{ $recentDossiersCount ?? 0 }}</span>
                    </div>

                    <div class="activity-line">
                        <strong>حجز جديد في المنشآت</strong>
                        <span class="badge-green">{{ $recentReservationsCount ?? 0 }}</span>
                    </div>

                    <div class="activity-line">
                        <strong>تذكرة جديدة</strong>
                        <span class="badge-orange">{{ $recentTicketsCount ?? 0 }}</span>
                    </div>

                    <div class="activity-line">
                        <strong>فعالية مضافة</strong>
                        <span class="badge-red">{{ $recentEventsCount ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="tv-panel">
                <div class="tv-panel-title">
                    الحجوزات اليومية
                    <span>أسبوع</span>
                </div>

                <div class="chart-large">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>

        </div>

        {{-- Quick menu --}}
        <div class="quick-grid">

            <a href="{{ route('news.index') }}" class="quick-card">
                <div class="quick-icon">📰</div>
                <div class="quick-title">الأخبار</div>
                <div class="quick-count">{{ \App\Models\News::count() }}</div>
            </a>

            <a href="{{ route('events.index') }}" class="quick-card">
                <div class="quick-icon">📅</div>
                <div class="quick-title">الأحداث</div>
                <div class="quick-count">{{ \App\Models\Event::count() }}</div>
            </a>

            <a href="{{ route('admin.dossiers.index') }}" class="quick-card">
                <div class="quick-icon">🗂️</div>
                <div class="quick-title">دراسة الملفات</div>
                <div class="quick-count">{{ $dossiersCount ?? 0 }}</div>
            </a>

            <a href="{{ route('admin.clubs.index') }}" class="quick-card">
                <div class="quick-icon">🏊‍♂️</div>
                <div class="quick-title">النوادي الرياضية</div>
                <div class="quick-count">{{ $clubsCount ?? 0 }}</div>
            </a>

            <a href="{{ route('persons.index') }}" class="quick-card">
                <div class="quick-icon">👥</div>
                <div class="quick-title">المنخرطون</div>
                <div class="quick-count">{{ $totalAgeRegistrations ?? 0 }}</div>
            </a>

            <a href="{{ route('admin.activities.index') }}" class="quick-card">
                <div class="quick-icon">🏋️‍♂️</div>
                <div class="quick-title">التخصصات الرياضية</div>
                <div class="quick-count">{{ \App\Models\Activity::count() }}</div>
            </a>

            <a href="{{ route('admin.complexes.index') }}" class="quick-card">
                <div class="quick-icon">🏟️</div>
                <div class="quick-title">المنشآت الرياضية</div>
                <div class="quick-count">{{ \App\Models\Complex::count() }}</div>
            </a>

            <a href="{{ route('admin.schedules.index') }}" class="quick-card">
                <div class="quick-icon">⏰</div>
                <div class="quick-title">الأفواج والتسعيرة</div>
                <div class="quick-count">{{ \App\Models\Schedule::count() }}</div>
            </a>

            <a href="{{ route('reservations.index') }}" class="quick-card">
                <div class="quick-icon">📝</div>
                <div class="quick-title">توزيع الأفواج</div>
                <div class="quick-count">{{ \App\Models\Reservation::count() }}</div>
            </a>

            <a href="{{ route('seasons.index') }}" class="quick-card">
                <div class="quick-icon">🗓️</div>
                <div class="quick-title">رزنامة التسجيلات</div>
                <div class="quick-count">{{ \App\Models\Season::count() }}</div>
            </a>

            <a href="{{ route('matches.index') }}" class="quick-card">
                <div class="quick-icon">⚽</div>
                <div class="quick-title">المباريات</div>
                <div class="quick-count">{{ \App\Models\MatchModel::count() }}</div>
            </a>

            <a href="{{ route('teams.index') }}" class="quick-card">
                <div class="quick-icon">🏆</div>
                <div class="quick-title">الفرق</div>
                <div class="quick-count">{{ $teamsCount ?? 0 }}</div>
            </a>

            <a href="{{ route('tickets.index') }}" class="quick-card">
                <div class="quick-icon">🎫</div>
                <div class="quick-title">التذاكر</div>
                <div class="quick-count">{{ \App\Models\Ticket::count() }}</div>
            </a>

        </div>

    </main>
</div>
@php
    $weeklyReservationsData = $weeklyReservations ?? [4, 7, 6, 8, 9, 5, 3];
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {

    const fontFamily = 'Cairo';

    const reservationsCtx = document.getElementById('reservationsChart');
    const reservationsData = @json(array_values($chartReservations ?? array_fill(0, 12, 0)));

    if (reservationsCtx) {
        new Chart(reservationsCtx, {
            type: 'line',
            data: {
                labels: [
                    'جانفي', 'فيفري', 'مارس', 'أفريل',
                    'ماي', 'جوان', 'جويلية', 'أوت',
                    'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
                ],
                datasets: [{
                    label: 'عدد الحجوزات',
                    data: reservationsData,
                    borderColor: '#18a8c7',
                    backgroundColor: 'rgba(24,168,199,.13)',
                    borderWidth: 3,
                    pointRadius: 3,
                    pointBackgroundColor: '#18a8c7',
                    tension: .38,
                    fill: true
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
                        backgroundColor: '#0b2545',
                        titleFont: { family: fontFamily, size: 13, weight: '700' },
                        bodyFont: { family: fontFamily, size: 13 },
                        padding: 12,
                        cornerRadius: 4
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#eef2f7' },
                        ticks: {
                            color: '#64748b',
                            font: { family: fontFamily, size: 11, weight: '700' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#eef2f7' },
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            font: { family: fontFamily, size: 11 }
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

        new Chart(ageCtx, {
            type: 'doughnut',
            data: {
                labels: ageLabels.length ? ageLabels : ['لا توجد بيانات'],
                datasets: [{
                    data: ageValues.length ? ageValues : [1],
                    backgroundColor: [
                        '#18a8c7',
                        '#00a86b',
                        '#e74c3c',
                        '#f39c12',
                        '#0b2545',
                        '#6d5dfc',
                        '#1f9ed4'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    cutout: '62%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: fontFamily, size: 10, weight: '700' },
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        rtl: true,
                        textDirection: 'rtl',
                        backgroundColor: '#0b2545',
                        padding: 12,
                        cornerRadius: 4
                    }
                }
            }
        });
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
                    backgroundColor: ['#00a86b', '#e74c3c', '#94a3b8'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    cutout: '65%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: fontFamily, size: 10, weight: '700' },
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        rtl: true,
                        textDirection: 'rtl',
                        backgroundColor: '#0b2545',
                        padding: 12,
                        cornerRadius: 4
                    }
                }
            }
        });
    }

    const dailyCtx = document.getElementById('dailyChart');

    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
                datasets: [{
                    label: 'الحجوزات',
                    data: @json($weeklyReservationsData),
                    backgroundColor: '#cbd5e1',
                    borderRadius: 3,
                    barThickness: 24
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
                        backgroundColor: '#0b2545',
                        padding: 12,
                        cornerRadius: 4
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#64748b',
                            font: { family: fontFamily, size: 11, weight: '700' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#eef2f7' },
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            font: { family: fontFamily, size: 11 }
                        }
                    }
                }
            }
        });
    }

});
</script>

@endsection