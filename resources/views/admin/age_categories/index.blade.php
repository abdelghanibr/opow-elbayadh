@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl">

    <div class="d-flex justify-content-between mb-3">
        <h4>👶 الفئات العمرية</h4>
        <a href="{{ route('age-categories.create') }}" class="btn btn-success">
            ➕ إضافة فئة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>من</th>
                <th>إلى</th>
                <th>التحكم</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->min_age }}</td>
                    <td>{{ $c->max_age }}</td>
                    <td>
                        <a href="{{ route('age-categories.edit', $c) }}"
                           class="btn btn-warning btn-sm">✏</a>

                        <form action="{{ route('age-categories.destroy', $c) }}"
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('حذف؟')">
                                🗑
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
