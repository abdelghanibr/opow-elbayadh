@extends('layouts.app')
@include('admin.partials.theme-admin')

@section('content')
<div class="container py-5" style="direction: rtl;">

    <h3 class="fw-bold mb-4">🎟️ جميع التذاكر</h3>

    <div class="card-modern p-4">

        <div class="table-responsive">
            <table id="ticketsTable" class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>المباراة</th>
                        <th>نوع المقعد</th>
                        <th>المشتري</th>
                        <th>الهاتف</th>
                        <th>الحالة</th>
                        <th>QR</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ $ticket->match->homeTeam->name }} ضد {{ $ticket->match->awayTeam->name }}
                        </td>

                        <td>{{ $ticket->seatType->name }}</td>
                        <td>{{ $ticket->buyer_name }}</td>
                        <td>{{ $ticket->buyer_phone }}</td>
                        <td>{{ $ticket->status }}</td>

                        <td>
                            @if($ticket->qr_code)
                                <img src="{{ asset('storage/'.$ticket->qr_code) }}" width="70">
                            @else —
                            @endif
                        </td>

                        <td>
                            <form action="{{ route('admin.tickets.destroy', $ticket->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('حذف التذكرة؟')" class="btn btn-danger btn-sm rounded-pill px-3">🗑 حذف</button>
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

@push('js')
@include('admin.partials.datatable-script', ['tableId' => '#ticketsTable'])
@endpush
