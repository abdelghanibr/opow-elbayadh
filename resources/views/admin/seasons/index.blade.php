@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between mb-4">
             <h3 class="fw-bold">📆 رزنامة التسجيل</h3>>
        <a href="{{ route('seasons.create') }}" class="btn btn-success">
            ➕ إضافة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold text-center">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>النوع</th>
                <th>بداية</th>
                <th>نهاية</th>
                <th width="160">إجراءات</th>
            </tr>
        </thead>
        <tbody>
        @foreach($seasons as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->name }}</td>
                <td>{{ \App\Models\Season::TYPES[$s->type_season] }}</td>
                <td>{{ $s->date_debut }}</td>
                <td>{{ $s->date_fin }}</td>
                <td>
                    <a href="{{ route('seasons.edit',$s) }}" class="btn btn-sm btn-primary">✏</a>
                    <form action="{{ route('seasons.destroy',$s) }}"
                          method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"
                                onclick="return confirm('حذف؟')">🗑</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
