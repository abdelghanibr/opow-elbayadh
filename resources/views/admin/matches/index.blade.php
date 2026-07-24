@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">

    <h3 class="mb-4 fw-bold">📅 المباريات المبرمجة</h3>

    <div class="mb-3 text-end">
        <a href="{{ route('matches.create') }}" class="btn btn-primary">
            ➕ إضافة مباراة
        </a>
    </div>

    <div class="table-responsive">
        <table id="matchesTable" class="table table-bordered table-striped table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>الفريق المستضيف</th>
                    <th>الفريق الضيف</th>
                    <th>المركب</th>
                    <th>التاريخ</th>
                    <th>الوقت</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matches as $m)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $m->homeTeam->name }}</td>
                    <td>{{ $m->awayTeam->name }}</td>
                    <td>{{ $m->complex->nom }}</td>
                    <td>{{ $m->match_date }}</td>
                    <td>{{ $m->match_time }}</td>
                    <td>{{ $m->status }}</td>

                    <td>
                        <a href="{{ route('matches.edit', $m->id) }}" class="btn btn-sm btn-warning">✏ تعديل</a>

                        <form action="{{ route('matches.destroy', $m->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('حذف المباراة؟')" class="btn btn-sm btn-danger">🗑 حذف</button>
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
@include('admin.partials.datatable-script', ['tableId' => '#matchesTable'])
@endpush
