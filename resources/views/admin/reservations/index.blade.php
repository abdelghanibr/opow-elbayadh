@extends('layouts.app')

@section('content')
<div class="container-fluid" style="direction: rtl">

{{-- ================= FILTRES (ADMIN ONLY) ================= --}}
@auth
@if(auth()->user()->type === 'admin')

<div class="card mb-3">
    <div class="card-body">

        <div class="d-flex flex-wrap gap-3 align-items-end">

            {{-- COMPLEX --}}
            <div style="min-width:220px">
                <label class="fw-bold mb-1">المركب</label>
                <select id="filterComplex" class="form-control">
                    <option value="">الكل</option>
                    @foreach($complexes as $complex)
                        <option value="{{ $complex->id }}">
                            {{ $complex->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ACTIVITY --}}
            <div style="min-width:220px">
                <label class="fw-bold mb-1">النشاط</label>
                <select id="filterActivity" class="form-control">
                    <option value="">الكل</option>
                    @foreach($activities as $activity)
                        <option value="{{ $activity->id }}">
                            {{ $activity->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- SCHEDULE --}}
<div style="min-width:220px">
    <label class="fw-bold mb-1">الفوج</label>
    <select id="filterSchedule" class="form-control">
        <option value="">الكل</option>
        @foreach($schedules as $schedule)
            <option value="{{ $schedule->id }}">
                {{ $schedule->groupe }}
            </option>
        @endforeach
    </select>
</div>


        </div>

    </div>
</div>

@endif
@endauth

{{-- ================= MESSAGE SUCCESS ================= --}}
@if(session('success'))
    <div class="alert alert-success fw-bold">
        {{ session('success') }}
    </div>
@endif

{{-- ================= TABLE ================= --}}
<div class="card shadow-sm">
<div class="card-body">

<h4 class="mb-3 fw-bold">📅 توزيع الافواج</h4>
{{-- ================= TOTAL PRICE ================= --}}
<div class="alert alert-success fw-bold text-center mb-3">
    💰 المجموع المدفوع فعليًا :
    <span id="totalPaidPrice" dir="ltr">  </span>
</div>


<table id="schedulesTable"
       class="table table-bordered table-hover align-middle text-center">
<thead class="table-dark">
<tr>
    <th>#</th>
    <th>المستخدم</th>
    <th>النوع</th>
   <th>الفوج</th>
    <th>النشاط</th>
       <th>المركب</th> 
    <th>تاريخ البداية</th>
    <th>تاريخ النهاية</th>
    <th>الحصص (يوم / تاريخ / وقت)</th>
    <th>الأماكن</th>
    <th>السعر</th>
    <th>الحالة</th>
    <th>الدفع</th>
    <th>إجراءات</th>
</tr>
</thead>

<tbody>
@foreach($reservations as $r)
<tr
    data-complex="{{ $r->complexActivity->complex->id ?? '' }}"
    data-activity="{{ $r->complexActivity->activity->id ?? '' }}"
    data-user-type="{{ $r->user->type ?? '' }}"
     data-schedule="{{ $r->schedule->id ?? '' }}"
     data-price="{{ $r->total_price ?? 0 }}"
       data-paid="{{ $r->payment_status === 'paid' ? 1 : 0 }}"
>


<td>{{ $r->id }}</td>

<td>{{ $r->user->name ?? '—' }}</td>
<td>
    <span class="badge bg-info">
        {{ match($r->user->type ?? '') {
            'club'    => 'نادي',
            'company' => 'مؤسسة',
            default   => 'فرد'
        } }}
    </span>
</td>
<td>{{ $r->schedule->groupe ?? '—' }}</td>
<td>{{ $r->complexActivity->activity->title ?? '—' }}</td>

<td>{{ $r->complexActivity->complex->nom ?? '—' }}</td>


<td>{{ \Carbon\Carbon::parse($r->start_date)->format('Y-m-d') }}</td>
<td>{{ \Carbon\Carbon::parse($r->end_date)->format('Y-m-d') }}</td>



{{-- TIME SLOTS --}}
{{-- 🟦 عرض time_slots --}}
          <td class="text-start">

@if(!empty($r->time_slots))
    @foreach($r->time_slots as $slot)
        @php
            $daysArabic = [
                0 => 'الأحد',
                1 => 'الإثنين',
                2 => 'الثلاثاء',
                3 => 'الأربعاء',
                4 => 'الخميس',
                5 => 'الجمعة',
                6 => 'السبت',
                7 => 'السبت',
            ];

            $dayName = $daysArabic[$slot['day_number'] ?? null] ?? '';
        @endphp

        <div class="badge bg-secondary d-block mb-1 text-start">
            📅 {{ $dayName }}<br>
            ⏱ {{ $slot['start'] ?? '?' }} → {{ $slot['end'] ?? '?' }}
        </div>
    @endforeach
@else
    <span class="text-muted">
        ⏱ {{ $r->start_time }} → {{ $r->end_time }}
    </span>
@endif




</td>

<td>{{ $r->qty_places }}</td>

<td>
    <span dir="ltr">{{ number_format($r->total_price, 0, ',', ' ') }} دج</span>
</td>



<td>
    @php
        $statusClass = match($r->statut) {
            'confirmee' => 'success',
            'annulee'   => 'danger',
            default     => 'warning',
        };
    @endphp
    <span class="badge bg-{{ $statusClass }}">
        {{ ucfirst($r->statut) }}
    </span>
</td>


<td class="text-nowrap">

   {{-- BADGE --}}
<span id="payBadge{{ $r->id }}"
      class="badge bg-{{ $r->payment_status==='paid'?'success':'secondary' }}">
    {{ $r->payment_status==='paid'?'مدفوع':'غير مدفوع' }}
</span>

@if(auth()->user()->type === 'admin')

    @if($r->payment_status !== 'paid')

        {{-- BOUTON CONFIRMER --}}
        <button type="button"
                id="payBtn{{ $r->id }}"
                class="btn btn-sm btn-outline-success ms-1"
                onclick="togglePayment({{ $r->id }})">
            تأكيد الدفع
        </button>

    @else

        {{-- UPDATED BY + DATE --}}
      <div id="payInfo{{ $r->id }}" class="small text-muted mt-1">

    <div>👤 {{ $r->updater->name ?? '—' }}</div>

    <div>📅 {{ $r->updated_at?->format('Y-m-d H:i') ?? '—' }}</div>

    <div>🧾 مرجع الدفع: {{ $r->payment->order_id ?? '—' }}</div>

</div>


        {{-- BOUTON ANNULER --}}
        <button type="button"
                id="payBtn{{ $r->id }}"
                class="btn btn-sm btn-outline-danger ms-1"
                onclick="togglePayment({{ $r->id }})">
            إلغاء الدفع
        </button>

    @endif

@endif

</td>



</td>


<td class="text-nowrap">
    <button class="btn btn-sm btn-outline-dark"
            onclick="printReservation({{ $r->id }})">🖨️</button>

    

    <form action="{{ route('reservations.destroy',$r) }}"
          method="POST" class="d-inline">
        @csrf @method('DELETE')
        <button onclick="return confirm('حذف؟')"
                class="btn btn-sm btn-danger">🗑</button>
    </form>
</td>

</tr>
@endforeach
</tbody>
</table>

</div>
</div>
</div>
@endsection

@push('css')

<style>
form {
    display:block!important;
    opacity:1!important;
    visibility:visible!important;
}
</style>
@endpush

@push('js')

@include('admin.partials.datatable-script', ['tableId' => '#schedulesTable'])
<script>
document.addEventListener('DOMContentLoaded', function () {

    const filterComplex  = document.getElementById('filterComplex');
    const filterActivity = document.getElementById('filterActivity');
    const filterSchedule = document.getElementById('filterSchedule');

    const rows = document.querySelectorAll('#schedulesTable tbody tr');
    const totalPaidEl = document.getElementById('totalPaidPrice');

    if (!rows.length || !totalPaidEl) return;

    // format DZD
    function formatDZD(amount) {
        return new Intl.NumberFormat('fr-FR').format(amount) + ' دج';
    }

    function applyFilters() {

        const c = filterComplex?.value || '';
        const a = filterActivity?.value || '';
        const s = filterSchedule?.value || '';

        let totalPaid = 0;

        rows.forEach(row => {

            const rc = row.dataset.complex || '';
            const ra = row.dataset.activity || '';
            const rs = row.dataset.schedule || '';
            const price = parseFloat(row.dataset.price || 0);
            const isPaid = row.dataset.paid === '1';

            let show = true;

            if (c && rc !== c) show = false;
            if (a && ra !== a) show = false;
            if (s && rs !== s) show = false;

            row.style.display = show ? '' : 'none';

            // ✅ حساب المدفوع فعليًا فقط
            if (show && isPaid) {
                totalPaid += price;
            }
        });

totalPaidEl.innerHTML =
    'دج <span dir="ltr">' +
      Math.round(totalPaid).toLocaleString('fr-FR')  +
    '</span> ';


    }

    // Events
    filterComplex?.addEventListener('change', applyFilters);
    filterActivity?.addEventListener('change', applyFilters);
    filterSchedule?.addEventListener('change', applyFilters);

    // Initial calculation
    applyFilters();
});

// Impression
function printReservation(id) {
    window.open(
        "{{ url('/reservations') }}/" + id + "/print",
        "_blank",
        "width=900,height=1200"
    );
}
function togglePayment(id) {

    const badge = document.getElementById('payBadge' + id);
    const isPaid = badge && badge.innerText.trim() === "مدفوع";

    // ===== Message clair =====
    const message = isPaid
        ? "⚠️ هل تريد إلغاء تسجيل هذا الدفع؟\nسيتم اعتبار الحجز غير مدفوع."
        : "💳 هل تؤكد أن هذا الحجز تم دفعه فعليًا؟";

    if (!confirm(message)) return;

    fetch("{{ url('/reservations') }}/" + id + "/toggle-payment", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {

        if (data.status !== 'ok') return;

        const btn = document.getElementById('payBtn' + id);
        let info  = document.getElementById('payInfo' + id);

        // ===== PAID =====
        if (data.payment_status === 'paid') {

            badge.className = "badge bg-success";
            badge.innerText = "مدفوع";

            btn.className = "btn btn-sm btn-outline-danger ms-1";
            btn.innerText = "إلغاء الدفع";

            if (!info) {
                info = document.createElement("div");
                info.id = "payInfo" + id;
                info.className = "small text-muted mt-1";
                btn.parentNode.insertBefore(info, btn);
            }

            info.innerHTML =
                (data.updated_by ?? '') + "<br>" +
                (data.updated_at ?? '');

        }

        // ===== UNPAID =====
        else {

            badge.className = "badge bg-secondary";
            badge.innerText = "غير مدفوع";

            btn.className = "btn btn-sm btn-outline-success ms-1";
            btn.innerText = "تأكيد الدفع";

            if (info) info.remove();
        }

        if (window.applyFilters) applyFilters();
    });
}


</script>


@endpush

