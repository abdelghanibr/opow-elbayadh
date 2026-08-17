@extends('layouts.app')

@section('content')

<style>
    .reservation-page {
        direction: rtl;
        text-align: right;
        max-width: 1200px;
        margin: auto;
        padding: 24px 12px;
    }

    .info-alert-custom {
        background: #e8f4ff;
        border: 1px solid #b6ddff;
        color: #0a4f88;
        border-radius: 14px;
        padding: 14px 16px;
        line-height: 1.8;
    }

    .warning-alert-custom {
        background: #fff7e6;
        border: 1px solid #ffd28a;
        color: #7a4a00;
        border-radius: 14px;
        padding: 14px 16px;
        line-height: 1.8;
    }

    .reservation-header {
        background: linear-gradient(to left, #0a4f88, #0a8a67);
        border-radius: 16px;
        color: #fff;
        padding: 16px;
        margin-bottom: 22px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }

    .reservation-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .reservation-header-title {
        font-size: 20px;
        font-weight: 800;
        margin: 0;
    }

    .reservation-table-card {
        background: #fff;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 5px 18px rgba(0,0,0,0.07);
        overflow: hidden;
    }

    .reservation-table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .reservation-table {
        min-width: 1000px;
        margin-bottom: 0;
    }

    .reservation-table th,
    .reservation-table td {
        vertical-align: middle;
        white-space: nowrap;
        font-size: 14px;
        padding: 10px;
    }

    .time-slot-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 6px 10px;
        margin-bottom: 5px;
        font-size: 13px;
        border: 1px solid #eee;
    }

    .reservation-mobile-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 5px 18px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 14px;
    }

    .reservation-mobile-card .card-body {
        padding: 15px;
    }

    .mobile-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
    }

    .mobile-activity-title {
        font-size: 16px;
        font-weight: 800;
        margin: 0;
        color: #0a4f88;
        line-height: 1.5;
    }

    .mobile-reservation-id {
        background: #eef6ff;
        color: #0a4f88;
        border-radius: 999px;
        padding: 5px 9px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .mobile-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f1f1;
        font-size: 14px;
    }

    .mobile-row:last-child {
        border-bottom: none;
    }

    .mobile-label {
        color: #6c757d;
        font-weight: 700;
        flex: 0 0 42%;
    }

    .mobile-value {
        color: #222;
        font-weight: 600;
        text-align: left;
        flex: 1;
    }

    .mobile-time-slots {
        margin-top: 8px;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 10px;
        border: 1px solid #eee;
    }

    .mobile-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-top: 14px;
    }

    .btn {
        border-radius: 10px;
        font-weight: 700;
    }

    .badge {
        border-radius: 8px;
        padding: 7px 9px;
        font-size: 12px;
    }

    .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        direction: rtl;
        text-align: right;
    }

    .modal-title {
        font-weight: 800;
    }

    .modal-body {
        max-height: 72vh;
        overflow-y: auto;
    }

    .modal-footer {
        gap: 8px;
        flex-wrap: wrap;
    }

    .renew-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .renew-grid .full {
        grid-column: 1 / -1;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        min-height: 44px;
    }

    .empty-state {
        background: #fff;
        border: 1px dashed #b6ddff;
        color: #0a4f88;
        border-radius: 16px;
        padding: 30px 18px;
        text-align: center;
        font-weight: 700;
    }

    @media (max-width: 991.98px) {
        .reservation-page {
            padding: 14px 10px;
        }

        .reservation-header {
            padding: 14px;
            border-radius: 14px;
        }

        .reservation-header-content {
            align-items: stretch;
        }

        .reservation-header-title {
            font-size: 18px;
        }

        .reservation-header .btn {
            width: 100%;
            padding: 10px;
        }

        .info-alert-custom,
        .warning-alert-custom {
            font-size: 13px;
            padding: 12px;
        }

        .modal-dialog {
            margin: 8px;
        }

        .modal-content {
            border-radius: 14px;
        }

        .modal-body {
            padding: 14px;
            max-height: 75vh;
        }

        .modal-footer {
            display: grid;
            grid-template-columns: 1fr;
            padding: 12px;
        }

        .modal-footer .btn {
            width: 100%;
            margin: 0;
        }

        .renew-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .mobile-row {
            flex-direction: column;
            gap: 3px;
        }

        .mobile-label,
        .mobile-value {
            flex: auto;
            text-align: right;
        }

        .mobile-actions {
            grid-template-columns: 1fr;
        }

        .mobile-card-top {
            flex-direction: column;
        }

        .mobile-reservation-id {
            width: fit-content;
        }
    }
</style>

<div class="reservation-page">

    {{-- =====================================================
        قواعد التجديد الجديدة
        =====================================================
        1. يظهر زر التجديد فقط إذا كان الحجز مدفوعًا:
           payment_status = paid

        2. يمكن التجديد ابتداءً من 10 أيام قبل نهاية الحجز:
           end_date - 10 jours

        3. يمكن التجديد بعد نهاية الحجز بحد أقصى يومين:
           end_date + 2 jours

        4. بعد مرور يومين من تاريخ نهاية الحجز، يتم إغلاق التجديد:
           today > end_date + 2 jours

        مثال:
        إذا كان تاريخ نهاية الحجز هو 30/06/2026:
        التجديد متاح من 20/06/2026 إلى 02/07/2026.
        ابتداءً من 03/07/2026 يصبح التجديد مغلقًا.
    ====================================================== --}}

    <div class="alert warning-alert-custom mb-3">
        <div class="fw-bold mb-1">⚠️ قواعد تجديد الحجز</div>
        يمكنكم تجديد حجزكم فقط إذا كان الحجز <b>مدفوعًا</b>.
        ويكون التجديد متاحًا ابتداءً من <b>10 أيام قبل نهاية الحجز</b>
        إلى غاية <b>يومين بعد نهاية الحجز</b> كحد أقصى.
        بعد انتهاء هذه المهلة، يتم غلق إمكانية التجديد.
    </div>

    <div class="alert info-alert-custom mb-4">
        <div class="fw-bold mb-1">ℹ️ تغيير الفوج</div>
        في حال رغبتكم في تغيير الفوج الحالي، يجب القيام بحجز جديد واختيار الفوج المطلوب بعد التحقق من توفر الأماكن فيه،
        وذلك بعد انتهاء فترة التجديد الخاصة بحجزكم الحالي.
    </div>

    <div class="reservation-header">
        <div class="reservation-header-content">
            <h1 class="reservation-header-title">📋 حجوزاتي</h1>

            <a href="{{ route('activities.index') }}" class="btn btn-light fw-bold">
                ➕ حجز جديد
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger text-center fw-bold mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success text-center fw-bold mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(isset($availableCredit) && $availableCredit > 0)
        <div class="alert alert-info d-flex justify-content-between align-items-center gap-2 flex-wrap" dir="rtl">
            <div>
                <strong>لديك رصيد تعويضي متاح:</strong>
                {{ number_format($availableCredit, 2, ',', ' ') }} دج
            </div>

            <span class="badge bg-primary">
                سيتم خصمه من الحجز القادم
            </span>
        </div>
    @endif

    @if($reservations->count())

        {{-- ========================= --}}
        {{-- Desktop Table --}}
        {{-- ========================= --}}
        <div class="d-none d-lg-block">
            <div class="reservation-table-card">
                <div class="reservation-table-wrapper">
                    <table class="table table-bordered table-striped align-middle text-center reservation-table">
                        <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>النشاط</th>
                            <th>محجوز لـ</th>
                            <th>الموسم</th>
                            <th>من</th>
                            <th>إلى</th>
                            <th>الساعات</th>
                            <th>الجدول</th>
                            <th>السعر</th>
                            <th>الدفع</th>
                            <th>التحكم</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($reservations as $r)
                            @php
                                $paymentStatusMap = [
                                    'paid'    => ['label' => 'مدفوع', 'class' => 'bg-success'],
                                    'pending' => ['label' => 'قيد الانتظار', 'class' => 'bg-warning text-dark'],
                                    'failed'  => ['label' => 'فشل الدفع', 'class' => 'bg-danger'],
                                ];

                                $payment = $paymentStatusMap[$r->payment_status] ?? [
                                    'label' => 'غير معروف',
                                    'class' => 'bg-secondary'
                                ];

                                $endDate = $r->end_date
                                    ? \Carbon\Carbon::parse($r->end_date)->startOfDay()
                                    : null;

                                $today = now()->startOfDay();

                                $renewStartDate = $endDate
                                    ? $endDate->copy()->subDays(10)
                                    : null;

                                $renewEndDate = $endDate
                                    ? $endDate->copy()->addDays(2)
                                    : null;

                                $canRenew = $r->payment_status === 'paid'
                                    && $endDate
                                    && $today->betweenIncluded($renewStartDate, $renewEndDate);

                                $isExpiredRenewAllowed = $r->payment_status === 'paid'
                                    && $endDate
                                    && $today->gt($endDate)
                                    && $today->lte($renewEndDate);

                                $isExpiredRenewClosed = $r->payment_status === 'paid'
                                    && $endDate
                                    && $today->gt($renewEndDate);
                            @endphp

                            <tr>
                                <td>{{ $r->id }}</td>

                                <td>{{ optional($r->complexActivity?->activity)->title ?? '—' }}</td>

                                <td>
                                    @if($r->person)
                                        <span class="badge bg-info text-dark">
                                            👶 {{ $r->person->firstname }} {{ $r->person->lastname }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>{{ $r->season?->name ?? '—' }}</td>

                                <td>{{ $r->start_date?->format('Y-m-d') ?? '—' }}</td>

                                <td>
                                    {{ $r->end_date?->format('Y-m-d') ?? '—' }}

                                    @if($r->payment_status === 'paid' && $endDate)
                                        <div class="small text-muted mt-1">
                                            التجديد:
                                            {{ $renewStartDate->format('Y-m-d') }}
                                            →
                                            {{ $renewEndDate->format('Y-m-d') }}
                                        </div>
                                    @endif
                                </td>

                                <td>{{ $r->duration_hours ?? '—' }}</td>

                                <td>
                                    @forelse($r->time_slots ?? [] as $slot)
                                        <div class="time-slot-item">
                                            {{ $r->getDayName($slot['day_number']) }}
                                            {{ $slot['start'] }} → {{ $slot['end'] }}
                                        </div>
                                    @empty
                                        —
                                    @endforelse
                                </td>

                                <td>{{ number_format($r->total_price ?? 0) }} دج</td>

                                <td>
                                    <span class="badge {{ $payment['class'] }}">
                                        {{ $payment['label'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                        @if($r->payment_status === 'paid')

                                            @if($canRenew)
                                                <button type="button"
                                                        class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#renewModal{{ $r->id }}">
                                                    🔁 تجديد
                                                </button>

                                                @if($isExpiredRenewAllowed)
                                                    <span class="badge bg-warning text-dark">
                                                        انتهى ويمكن تجديده
                                                    </span>
                                                @endif
                                            @else
                                                @if($isExpiredRenewClosed)
                                                    <span class="badge bg-danger">
                                                        انتهت مهلة التجديد
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        التجديد غير متاح الآن
                                                    </span>
                                                @endif
                                            @endif

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-dark"
                                                    onclick="printReservation({{ $r->id }})">
                                                🖨️ وصل
                                            </button>

                                        @else
                                            <a href="{{ route('payments.pay', $r->id) }}"
                                               class="btn btn-sm btn-success">
                                                💳 الدفع
                                            </a>

                                            <form action="{{ route('reservations.destroy', $r->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('حذف الحجز؟');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ========================= --}}
        {{-- Mobile Cards --}}
        {{-- ========================= --}}
        <div class="d-lg-none">
            @foreach($reservations as $r)
                @php
                    $paymentStatusMap = [
                        'paid'    => ['label' => 'مدفوع', 'class' => 'bg-success'],
                        'pending' => ['label' => 'قيد الانتظار', 'class' => 'bg-warning text-dark'],
                        'failed'  => ['label' => 'فشل الدفع', 'class' => 'bg-danger'],
                    ];

                    $payment = $paymentStatusMap[$r->payment_status] ?? [
                        'label' => 'غير معروف',
                        'class' => 'bg-secondary'
                    ];

                    $endDate = $r->end_date
                        ? \Carbon\Carbon::parse($r->end_date)->startOfDay()
                        : null;

                    $today = now()->startOfDay();

                    $renewStartDate = $endDate
                        ? $endDate->copy()->subDays(10)
                        : null;

                    $renewEndDate = $endDate
                        ? $endDate->copy()->addDays(2)
                        : null;

                    $canRenew = $r->payment_status === 'paid'
                        && $endDate
                        && $today->betweenIncluded($renewStartDate, $renewEndDate);

                    $isExpiredRenewAllowed = $r->payment_status === 'paid'
                        && $endDate
                        && $today->gt($endDate)
                        && $today->lte($renewEndDate);

                    $isExpiredRenewClosed = $r->payment_status === 'paid'
                        && $endDate
                        && $today->gt($renewEndDate);
                @endphp

                <div class="card reservation-mobile-card">
                    <div class="card-body">

                        <div class="mobile-card-top">
                            <div>
                                <h6 class="mobile-activity-title">
                                    {{ $r->complexActivity?->activity?->title ?? '—' }}
                                </h6>

                                <div class="small text-muted mt-1">
                                    الموسم: {{ $r->season?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="mobile-reservation-id">
                                #{{ $r->id }}
                            </div>
                        </div>

                        @if($r->person)
                            <div class="mobile-row">
                                <div class="mobile-label">👶 محجوز لـ</div>
                                <div class="mobile-value">
                                    {{ $r->person->firstname }} {{ $r->person->lastname }}
                                </div>
                            </div>
                        @endif

                        <div class="mobile-row">
                            <div class="mobile-label">📅 من</div>
                            <div class="mobile-value">
                                {{ $r->start_date?->format('Y-m-d') ?? '—' }}
                            </div>
                        </div>

                        <div class="mobile-row">
                            <div class="mobile-label">📅 إلى</div>
                            <div class="mobile-value">
                                {{ $r->end_date?->format('Y-m-d') ?? '—' }}
                            </div>
                        </div>

                        @if($r->payment_status === 'paid' && $endDate)
                            <div class="mobile-row">
                                <div class="mobile-label">🔁 فترة التجديد</div>
                                <div class="mobile-value">
                                    {{ $renewStartDate->format('Y-m-d') }}
                                    →
                                    {{ $renewEndDate->format('Y-m-d') }}
                                </div>
                            </div>
                        @endif

                        <div class="mobile-row">
                            <div class="mobile-label">⏱️ الساعات</div>
                            <div class="mobile-value">
                                {{ $r->duration_hours ?? '—' }}
                            </div>
                        </div>

                        <div class="mobile-row">
                            <div class="mobile-label">💰 السعر</div>
                            <div class="mobile-value">
                                {{ number_format($r->total_price ?? 0) }} دج
                            </div>
                        </div>

                        <div class="mobile-row">
                            <div class="mobile-label">💳 الدفع</div>
                            <div class="mobile-value">
                                <span class="badge {{ $payment['class'] }}">
                                    {{ $payment['label'] }}
                                </span>
                            </div>
                        </div>

                        <div class="mobile-time-slots">
                            <div class="fw-bold mb-2">📌 الجدول</div>

                            @forelse($r->time_slots ?? [] as $slot)
                                <div class="time-slot-item">
                                    {{ $r->getDayName($slot['day_number']) }}
                                    {{ $slot['start'] }} → {{ $slot['end'] }}
                                </div>
                            @empty
                                <div class="text-muted small">لا يوجد جدول محدد</div>
                            @endforelse
                        </div>

                        @if($r->payment_status === 'paid')
                            <div class="mobile-actions">
                                @if($canRenew)
                                    <button type="button"
                                            class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#renewModal{{ $r->id }}">
                                        🔁 تجديد
                                    </button>
                                @else
                                    <button type="button"
                                            class="btn btn-secondary btn-sm"
                                            disabled>
                                        🔁 التجديد غير متاح
                                    </button>
                                @endif

                                <button type="button"
                                        class="btn btn-outline-dark btn-sm"
                                        onclick="printReservation({{ $r->id }})">
                                    🖨️ وصل
                                </button>
                            </div>

                            @if($isExpiredRenewAllowed)
                                <div class="alert alert-warning mt-2 mb-0 py-2 text-center fw-bold">
                                    انتهى الحجز ويمكن تجديده خلال مهلة يومين فقط.
                                </div>
                            @endif

                            @if($isExpiredRenewClosed)
                                <div class="alert alert-danger mt-2 mb-0 py-2 text-center fw-bold">
                                    انتهت مهلة التجديد.
                                </div>
                            @endif
                        @else
                            <div class="mobile-actions">
                                <a href="{{ route('payments.pay', $r->id) }}"
                                   class="btn btn-success btn-sm">
                                    💳 الدفع
                                </a>

                                <form action="{{ route('reservations.destroy', $r->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('حذف الحجز؟');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        🗑️ حذف
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>

        {{-- ========================= --}}
        {{-- Renew Modals --}}
        {{-- ========================= --}}
        @foreach($reservations as $r)
            @php
                $endDate = $r->end_date
                    ? \Carbon\Carbon::parse($r->end_date)->startOfDay()
                    : null;

                $today = now()->startOfDay();

                $renewStartDate = $endDate
                    ? $endDate->copy()->subDays(10)
                    : null;

                $renewEndDate = $endDate
                    ? $endDate->copy()->addDays(2)
                    : null;

                $canRenew = $r->payment_status === 'paid'
                    && $endDate
                    && $today->betweenIncluded($renewStartDate, $renewEndDate);
            @endphp

            @if($canRenew)
                <div class="modal fade" id="renewModal{{ $r->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
                        <form action="{{ route('reservations.renew.store', $r->id) }}" method="POST" class="w-100">
                            @csrf

                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        🔁 تجديد الحجز #{{ $r->id }}
                                    </h5>

                                    <button type="button"
                                            class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="alert alert-info fw-bold">
                                        فترة التجديد المسموحة لهذا الحجز:
                                        {{ $renewStartDate->format('Y-m-d') }}
                                        →
                                        {{ $renewEndDate->format('Y-m-d') }}
                                    </div>

                                    <div class="renew-grid">

                                        <div class="full">
                                            <label class="form-label fw-bold">🏷️ اختر الموسم</label>

                                            <select name="season_id"
                                                    class="form-control"
                                                    required
                                                    data-reservation-id="{{ $r->id }}"
                                                    onchange="fillSeasonDates(this); checkExistingReservation(this);">
                                                <option value="">— اختر الموسم —</option>

                                                @foreach($seasons as $season)
                                                    <option value="{{ $season->id }}"
                                                            data-start="{{ $season->date_debut }}"
                                                            data-end="{{ $season->date_fin }}">
                                                        {{ $season->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <div id="renewError-{{ $r->id }}"
                                                 class="alert alert-danger mt-2 d-none text-center fw-bold">
                                                ⚠ لديك حجز موجود بالفعل لهذا الموسم
                                            </div>
                                        </div>

                                        <div>
                                            <label class="form-label fw-bold">📅 من</label>
                                            <input type="date"
                                                   id="startDate-{{ $r->id }}"
                                                   name="start_date"
                                                   class="form-control"
                                                   readonly
                                                   required>
                                        </div>

                                        <div>
                                            <label class="form-label fw-bold">📅 إلى</label>
                                            <input type="date"
                                                   id="endDate-{{ $r->id }}"
                                                   name="end_date"
                                                   class="form-control"
                                                   readonly
                                                   required>
                                        </div>

                                        <div class="full">
                                            <div class="form-check mt-2">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="pay_now"
                                                       value="1"
                                                       id="payNow{{ $r->id }}">

                                                <label class="form-check-label fw-bold" for="payNow{{ $r->id }}">
                                                    💳 الدفع مباشرة بعد التجديد
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button"
                                            class="btn btn-secondary"
                                            data-bs-dismiss="modal">
                                        إلغاء
                                    </button>

                                    <button type="submit"
                                            id="renewSubmitBtn-{{ $r->id }}"
                                            class="btn btn-success">
                                        ✅ تأكيد التجديد
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endforeach

    @else
        <div class="empty-state">
            لا توجد حجوزات حالياً.
            <div class="mt-3">
                <a href="{{ route('activities.index') }}" class="btn btn-primary">
                    ➕ إنشاء حجز جديد
                </a>
            </div>
        </div>
    @endif

</div>

@endsection

@push('js')
<script>
function checkExistingReservation(select) {
    const seasonId = select.value;
    const reservationId = select.dataset.reservationId;

    const errorBox = document.getElementById('renewError-' + reservationId);
    const submitBtn = document.getElementById('renewSubmitBtn-' + reservationId);

    if (!errorBox || !submitBtn) {
        return;
    }

    if (!seasonId) {
        errorBox.classList.add('d-none');
        submitBtn.disabled = false;
        return;
    }

    fetch(`/reservations/${reservationId}/check-season/${seasonId}`)
        .then(function (res) {
            return res.json();
        })
        .then(function (data) {
            if (data.exists) {
                errorBox.classList.remove('d-none');
                submitBtn.disabled = true;
            } else {
                errorBox.classList.add('d-none');
                submitBtn.disabled = false;
            }
        })
        .catch(function (error) {
            console.error('Erreur check-season:', error);
        });
}

function fillSeasonDates(select) {
    const id = select.dataset.reservationId;
    const opt = select.options[select.selectedIndex];

    const startInput = document.getElementById('startDate-' + id);
    const endInput = document.getElementById('endDate-' + id);

    if (startInput) {
        startInput.value = opt.dataset.start || '';
    }

    if (endInput) {
        endInput.value = opt.dataset.end || '';
    }
}

function printReservation(id) {
    window.open("{{ url('/reservations') }}/" + id + "/print", "_blank");
}
</script>
@endpush
