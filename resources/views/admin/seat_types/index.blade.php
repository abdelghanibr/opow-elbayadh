@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">

    <h3 class="mb-4 fw-bold">🎫 أنواع المقاعد</h3>

    <div class="mb-3 text-end">
        <a href="{{ route('seat_types.create') }}" class="btn btn-primary">
            ➕ إضافة نوع مقعد
        </a>
    </div>

    <div class="table-responsive">
        <table id="seatTypesTable" class="table table-bordered table-striped table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>السعر</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($types as $t)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $t->name }}</td>
                    <td>{{ number_format($t->price, 2) }} دج</td>

                    <td>
                        <a href="{{ route('seat_types.edit', $t->id) }}" class="btn btn-sm btn-warning">✏ تعديل</a>

                        <form action="{{ route('seat_types.destroy', $t->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('حذف النوع؟')" class="btn btn-sm btn-danger">🗑 حذف</button>
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
@include('admin.partials.datatable-script', ['tableId' => '#seatTypesTable'])
@endpush
