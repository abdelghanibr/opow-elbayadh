@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">

    <h3 class="mb-4 fw-bold">⚽ الفرق الرياضية</h3>

    <div class="mb-3 text-end">
        <a href="{{ route('teams.create') }}" class="btn btn-primary">
            ➕ إضافة فريق
        </a>
    </div>

    <div class="table-responsive">
        <table id="teamsTable" class="table table-bordered table-striped table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>الشعار</th>
                    <th>الاسم</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teams as $t)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($t->logo)
                            <img src="{{ asset($t->logo) }}" width="80" class="rounded">
                        @else
                            <img src="{{ asset('images/placeholder.png') }}" width="80">
                        @endif
                    </td>
                    <td>{{ $t->name }}</td>

                    <td>
                        <a href="{{ route('teams.edit', $t->id) }}" class="btn btn-sm btn-warning">✏ تعديل</a>

                        <form action="{{ route('teams.destroy', $t->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('حذف الفريق؟')" class="btn btn-sm btn-danger">🗑 حذف</button>
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
@include('admin.partials.datatable-script', ['tableId' => '#teamsTable'])
@endpush
