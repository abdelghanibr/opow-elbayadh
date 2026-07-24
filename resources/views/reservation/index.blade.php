@extends('layouts.app')

@section('content')

<div class="container py-4" style="direction: rtl; text-align:right;">

    {{-- 🟦 Header --}}
    <div class="p-3 mb-4"
         style="background: linear-gradient(to right, #0a4f88, #0a8a67);
                border-radius: 10px;
                color: #fff;
                font-weight:600;">
        <div class="d-flex justify-content-between align-items-center">
            <span>📋 حجوزاتي</span>

            <a href="{{ route('reservations.create') }}" class="btn btn-light fw-bold">
                ➕ حجز جديد
            </a>
        </div>
    </div>

    {{-- 🔍 فلاتر --}}
    <div class="card p-3 shadow-sm mb-3">
        <div class="row g-3">

            <div class="col-md-3">
                <label>النشاط</label>
                <select id="filterActivity" class="form-control">
                    <option value="">الكل</option>
                    @foreach($activities as $a)
                        <option value="{{ $a->title }}">{{ $a->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>الموسم</label>
                <select id="filterSeason" class="form-control">
                    <option value="">الكل</option>
                    @foreach($seasons as $s)
                        <option value="{{ $s->name }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>الحالة</label>
                <select id="filterStatus" class="form-control">
                    <option value="">الكل</option>
                    <option value="pending">قيد الانتظار</option>
                    <option value="confirmed">مؤكد</option>
                    <option value="rejected">مرفوض</option>
                </select>
            </div>

            <div class="col-md-2">
                <label>الدفع</label>
                <select id="filterPayment" class="form-control">
                    <option value="">الكل</option>
                    <option value="paid">مدفوع</option>
                    <option value="unpaid">غير مدفوع</option>
                </select>
            </div>

        </div>
    </div>

    {{-- 📊 Table --}}
    <div class="card p-3 shadow-sm">

        <table id="reservationsTable"
               class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>النشاط</th>
                    <th>الموسم</th>
                    <th>من</th>
                    <th>إلى</th>
                    <th>الساعات</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>الدفع</th>
                    <th>التحكم</th>
                </tr>
            </thead>

            <tbody>
                @foreach($reservations as $r)
                <tr>
                    <td>{{ $r->id }}</td>

                    <td>
                        {{ $r->complexActivity->activity->title ?? '—' }}
                    </td>

                    <td>{{ $r->season->name ?? '—' }}</td>

                    <td>{{ $r->start_date?->format('Y-m-d') }}</td>
                    <td>{{ $r->end_date?->format('Y-m-d') }}</td>

                    <td>{{ $r->duration_hours ?? '—' }}</td>

                    <td>
                        {{ number_format($r->total_price ?? 0) }} دج
                    </td>

                    {{-- الحالة --}}
                    <td>
                        <span class="badge bg-{
                            { $r->status == 'confirmed' ? 'success' :
                               ($r->status == 'pending' ? 'warning' : 'danger') }
                        ">
                            {{ $r->status }}
                        </span>
                    </td>

                    {{-- الدفع --}}
                    <td>
                        <span class="badge bg-{
                            { $r->payment_status == 'paid' ? 'success' : 'secondary' }
                        ">
                            {{ $r->payment_status }}
                        </span>
                    </td>

                    {{-- التحكم --}}
                    <td>
                        <a href="{{ route('reservations.renew', $r->id) }}"
                           class="btn btn-sm btn-outline-primary">
                            🔁 تجديد
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>
</div>

@endsection

@push('js')

@include('admin.partials.datatable-script', ['tableId' => '#reservationsTable'])

<script>
$(document).ready(function() {

    let table = $('#reservationsTable').DataTable();

    $('#filterActivity, #filterSeason, #filterStatus, #filterPayment')
        .on('change', function () {
            table.draw();
        });

    $.fn.dataTable.ext.search.push(
        function(settings, data) {

            let activity = $('#filterActivity').val();
            let season   = $('#filterSeason').val();
            let status   = $('#filterStatus').val();
            let payment  = $('#filterPayment').val();

            let col_activity = data[1];
            let col_season   = data[2];
            let col_status   = data[7];
            let col_payment  = data[8];

            if (activity && col_activity !== activity) return false;
            if (season && col_season !== season) return false;
            if (status && col_status !== status) return false;
            if (payment && col_payment !== payment) return false;

            return true;
        }
    );
});
</script>

@endpush
