@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">

    <h3 class="fw-bold mb-3">👑 إدارة المسؤولين</h3>

    <a href="{{ route('admins.create') }}" class="btn btn-primary mb-3">
        ➕ إضافة مسؤول جديد
    </a>

    <table class="table table-bordered text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>البريد</th>
                <th>المجمع الرياضي</th> <!-- ⬅ عمود جديد -->
                <th>إجراءات</th>
            </tr>
        </thead>

        <tbody>
            @foreach($admins as $a)
            <tr>
                <td>{{ $a->id }}</td>
                <td>{{ $a->name }}</td>
                <td>{{ $a->email }}</td>

                <!-- عرض اسم المجمع أو لا يوجد -->
                <td>
                    @if($a->complex_id && $a->complex)
                        <span class="badge bg-success">{{ $a->complex->nom }}</span>
                    @else
                        <span class="text-muted">— بدون مجمع —</span>
                    @endif
                </td>

                <td>
                    <a href="{{ route('admins.edit', $a->id) }}"
                       class="btn btn-sm btn-warning">✏ تعديل</a>

                    <form action="{{ route('admins.delete', $a->id) }}"
                          method="POST"
                          class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"
                                onclick="return confirm('هل تريد حذف هذا المسؤول؟')">
                            🗑 حذف
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
