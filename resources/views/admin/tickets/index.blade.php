@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">

    <h3 class="mb-4 fw-bold">🎟️ جميع التذاكر</h3>

    <div class="table-responsive">
        <table id="ticketsTable" class="table table-bordered table-striped table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>المباراة</th>
                    <th>نوع المقعد</th>
                    <th>اسم المشتري</th>
                    <th>الهاتف</th>
                    <th>الحالة</th>
                    <th>QR</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $t)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $t->match->homeTeam->name }} ضد {{ $t->match->awayTeam->name }}
                    </td>
                    <td>{{ $t->seatType->name }}</td>
                    <td>{{ $t->buyer_name }}</td>
                    <td>{{ $t->buyer_phone }}</td>
                    <td>{{ $t->status }}</td>

                    <td>
                        @if($t->qr_code)
                            <img src="{{ asset('storage/'.$t->qr_code) }}" width="70">
                        @else
                            —
                        @endif
                    </td>

                    <td>
                        <form action="{{ route('tickets.destroy', $t->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('حذف التذكرة؟')" class="btn btn-sm btn-danger">🗑 حذف</button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.min.css">
@endpush

@push('js')
@include('admin.partials.datatable-script', ['tableId' => '#ticketsTable'])
@endpush
