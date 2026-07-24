@extends('layouts.app')

@section('content')

<div class="container py-4" style="direction: rtl; text-align:right; max-width:1200px">

<div class="alert mb-4"
     style="background:#e8f4ff; border:1px solid #b6ddff; color:#0a4f88; border-radius:12px;">
    <div class="fw-bold mb-1">ℹ️ ملاحظة هامة بخصوص تجديد الحجز</div>

    يمكنكم تجديد حجزكم قبل نهاية الفترة الحالية بـ <b>15 يومًا</b> لضمان
    الاحتفاظ بمكانكم في نفس الفوج.

    في حال عدم القيام بالتجديد خلال هذه الفترة، قد يتم إسناد المكان
    لمشترك آخر.
</div>

<div class="alert mb-4"
     style="background:#e8f4ff; border:1px solid #b6ddff; color:#0a4f88; border-radius:12px;">
    <div class="fw-bold mb-1">ℹ️ تغيير الفوج</div>

    في حال رغبتكم في تغيير الفوج الحالي، يجب القيام بحجز جديد
    واختيار الفوج المطلوب بعد التحقق من توفر الأماكن فيه،
    وذلك بعد انتهاء فترة التجديد الخاصة بحجزكم الحالي.
</div>

    {{-- 🟦 Header --}}
    <div class="p-3 mb-4"
         style="background: linear-gradient(to right, #0a4f88, #0a8a67);
                border-radius: 14px;
                color: #fff;
                font-weight:700;">
        <div class="d-flex justify-content-between align-items-center">
            <span>📋 حجوزاتي</span>
            <a href="{{ route('activities.index') }}" class="btn btn-light fw-bold">
                ➕ حجز جديد
            </a>
        </div>
    </div>
{{-- رسالة الخطأ الرئيسية --}}
@if(session('error'))
    <div class="alert alert-danger text-center fw-bold mb-4">
        {{ session('error') }}
    </div>
@endif

@if(isset($availableCredit) && $availableCredit > 0)
    <div class="alert alert-info d-flex justify-content-between align-items-center" dir="rtl">
        <div>
            <strong>لديك رصيد تعويضي متاح:</strong>
            {{ number_format($availableCredit, 2, ',', ' ') }} دج
        </div>

        <span class="badge bg-primary">
            سيتم خصمه من الحجز القادم
        </span>
    </div>
@endif
    {{-- ========================= --}}
    {{-- 🖥️ DESKTOP TABLE --}}
    {{-- ========================= --}}
    <div class="d-none d-lg-block">
        <div class="card shadow-sm p-3">
            <table class="table table-bordered table-striped align-middle text-center">
                <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>النشاط</th>
                    <th>الموسم</th>
                    <th>من</th>
                    <th>إلى</th>
                    <th>الساعات</th>
                    <th>الجدول</th>
               
                    <th> السعر</th>
                    <th>الدفع</th>
                    <th>التحكم</th>
                </tr>
                </thead>

                <tbody>
                @foreach($reservations as $r)
                    <tr>
                        <td>{{ $r->id }}</td>
                        <td>{{ optional($r->complexActivity?->activity)->title ?? '—' }}</td>
                        <td>{{ $r->season?->name ?? '—' }}</td>
                        <td>{{ $r->start_date?->format('Y-m-d') }}</td>
                        <td>{{ $r->end_date?->format('Y-m-d') }}</td>
                        <td>{{ $r->duration_hours ?? '—' }}</td>

                        <td>
                            @foreach($r->time_slots ?? [] as $slot)
                                <div class="bg-light rounded px-2 py-1 mb-1">
                                    {{ $r->getDayName($slot['day_number']) }}
                                    {{ $slot['start'] }} → {{ $slot['end'] }}
                                </div>
                            @endforeach
                        </td>

                        <td>{{ number_format($r->total_price ?? 0) }} دج</td>

                        <td>
                           @php
    $paymentStatusMap = [
        'paid'    => ['label' => 'مدفوع', 'class' => 'bg-success'],
        'pending' => ['label' => 'قيد الانتظار', 'class' => 'bg-warning'],
        'failed'  => ['label' => 'فشل الدفع', 'class' => 'bg-danger'],
    ];

    $payment = $paymentStatusMap[$r->payment_status] ?? [
        'label' => 'غير معروف',
        'class' => 'bg-secondary'
    ];
@endphp

<span class="badge {{ $payment['class'] }}">
    {{ $payment['label'] }}
</span>

                        </td>

                        <td class="d-flex gap-1 justify-content-center flex-wrap">
                            @if($r->payment_status === 'paid')
                                <button class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#renewModal{{ $r->id }}">
                                    🔁 تجديد
                                </button>

                                <button class="btn btn-sm btn-outline-dark"
                                        onclick="printReservation({{ $r->id }})">
                                    🖨️ وصل
                                </button>
                            @else
                                <a href="{{ route('payments.pay', $r->id) }}"
                                   class="btn btn-sm btn-success">💳 الدفع</a>

                                <form action="{{ route('reservations.destroy', $r->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('حذف الحجز؟');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">🗑️</button>
                                </form>
                            @endif
                        </td>
                    </tr>

                    {{-- ================= MODAL RENEW ================= --}}
                    <div class="modal fade" id="renewModal{{ $r->id  }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <form action="{{ route('reservations.renew.store', $r->id) }}" method="POST">
                                @csrf
                                <div class="modal-content" style="direction: rtl">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">🔁 تجديد الحجز</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row g-3">

                                            <div class="col-12">
                                                <label class="fw-bold">🏷️ اختر الموسم</label>
                                                <select name="season_id"
                                                        class="form-control"
                                                        required
                                                        data-reservation-id="{{ $r->id }}"
                                                        onchange="fillSeasonDates(this); ">
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

                                            <div class="col-md-6">
                                                <label>📅 من</label>
                                                <input type="date"
                                                       id="startDate-{{ $r->id }}"
                                                       name="start_date"
                                                       class="form-control"
                                                       readonly required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>📅 إلى</label>
                                                <input type="date"
                                                       id="endDate-{{ $r->id }}"
                                                       name="end_date"
                                                       class="form-control"
                                                       readonly required>
                                            </div>

                                        </div>

                                        <hr>

                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   name="pay_now"
                                                   value="1"
                                                   id="payNow{{ $r->id }}">
                                            <label class="form-check-label" for="payNow{{ $r->id }}">
                                                💳 الدفع مباشرة بعد التجديد
                                            </label>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
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
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- 📱 MOBILE CARDS --}}
    {{-- ========================= --}}
    <div class="d-lg-none">
        @foreach($reservations as $r)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-1">
                        {{ $r->complexActivity?->activity?->title ?? '—' }}
                    </h6>

                    <div class="small text-muted mb-2">
                        الموسم: {{ $r->season?->name ?? '—' }}
                    </div>

                    <div class="mb-2">
                        📅 {{ $r->start_date?->format('Y-m-d') }} → {{ $r->end_date?->format('Y-m-d') }}
                    </div>

                    <div class="mb-2">
                        💰 {{ number_format($r->total_price ?? 0) }} دج
                    </div>

                   
                    
                    @php
    $paymentStatusMap = [
        'paid'    => ['label' => 'مدفوع', 'class' => 'bg-success'],
        'pending' => ['label' => 'قيد الانتظار', 'class' => 'bg-warning'],
        'failed'  => ['label' => 'فشل الدفع', 'class' => 'bg-danger'],
    ];

    $payment = $paymentStatusMap[$r->payment_status] ?? [
        'label' => 'غير معروف',
        'class' => 'bg-secondary'
    ];
@endphp

<span class="badge {{ $payment['class'] }}">
    {{ $payment['label'] }}
</span>


                    <div class="d-flex gap-2 mt-3">
                        @if($r->payment_status === 'paid')
                            <button class="btn btn-primary btn-sm w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#renewModal{{ $r->id }}">
                                🔁 تجديد
                            </button>

                            <button class="btn btn-outline-dark btn-sm w-100"
                                    onclick="printReservation({{ $r->id }})">
                                🖨️ وصل
                            </button>
                        @else
                            <a href="{{ route('payments.pay', $r->id) }}"
                               class="btn btn-success btn-sm w-100">💳 الدفع</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>


{{-- 🔔 ملاحظة التجديد --}}


@endsection

@push('js')
<script>
function checkExistingReservation(select) {

    const seasonId = select.value;
    const reservationId = select.dataset.reservationId;
// 
    const errorBox = document.getElementById('renewError-' + reservationId);
    const submitBtn = document.getElementById('renewSubmitBtn-' + reservationId);

    if (!seasonId) {
        errorBox.classList.add('d-none');
        submitBtn.disabled = false;
     
        return;
    }

    fetch(`/reservations/${reservationId}/check-season/${seasonId}`)
        .then(res => res.json())
        .then(data => {

            if (data.exists) {

                // afficher message dans le modal
                errorBox.classList.remove('d-none');

                // désactiver bouton
                submitBtn.disabled = true;

                // afficher alert
                alert("⚠ لديك حجز موجود بالفعل لهذا الموسم");

            } else {

                errorBox.classList.add('d-none');
                submitBtn.disabled = false;

            }

        })
        .catch(error => {
            console.error('Erreur check-season:', error);
        });

}

function fillSeasonDates(select) {
    const id = select.dataset.reservationId;
    const opt = select.options[select.selectedIndex];
    document.getElementById('startDate-' + id).value = opt.dataset.start || '';
    document.getElementById('endDate-' + id).value = opt.dataset.end || '';
}

function printReservation(id) {
    window.open("{{ url('/reservations') }}/" + id + "/print", "_blank");
}
</script>
@endpush
