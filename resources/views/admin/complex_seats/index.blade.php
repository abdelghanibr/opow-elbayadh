@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">

    <h3 class="mb-4 fw-bold">💺 توزيع المقاعد حسب المركب</h3>

    <div class="mb-3 text-end">
        <a href="{{ route('complex_seats.create') }}" class="btn btn-primary">
            ➕ إضافة توزيع جديد
        </a>
    </div>

    <div class="table-responsive">
        <table id="complexSeatsTable" class="table table-bordered table-striped table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>المركب</th>
                    <th>نوع المقعد</th>
                    <th>إجمالي المقاعد</th>
                    <th>المقاعد المتاحة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($complexSeats as $seat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $seat->complex->nom }}</td>
                    <td>{{ $seat->seatType->name }}</td>
                    <td>{{ $seat->total_seats }}</td>
                    <td>{{ $seat->available_seats }}</td>

                    <td>
                        <a href="{{ route('complex_seats.edit', $seat->id) }}" class="btn btn-sm btn-warning">✏ تعديل</a>

                        <form action="{{ route('complex_seats.destroy', $seat->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('حذف التوزيع؟')" class="btn btn-sm btn-danger">🗑 حذف</button>
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
@include('admin.partials.datatable-script', ['tableId' => '#complexSeatsTable'])
@endpush
