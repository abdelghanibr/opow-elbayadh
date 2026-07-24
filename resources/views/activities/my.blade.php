@extends('layouts.app')

@section('content')

<div class="container py-4">
    
    <h2 class="fw-bold mb-4">🎯 أنشطتي المسجلة</h2>

    <table class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>النشاط</th>
                <th>الفترة</th>
                <th>المكان</th>
                <th>الحالة</th>
                <th>الدفع</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($my as $m)
            <tr>
                <td>{{ $m->title }}</td>
                <td>{{ $m->start_date }} → {{ $m->end_date }}</td>
                <td>{{ $m->location }}</td>

                <td>
                    @if($m->status == 'en_attente')
                        <span class="badge bg-warning">قيد المراجعة</span>
                    @elseif($m->status == 'accepte')
                        <span class="badge bg-success">مقبول</span>
                    @else
                        <span class="badge bg-danger">مرفوض</span>
                    @endif
                </td>

                <td>
                    @if($m->payment_status == 'paye')
                        <span class="badge bg-success">مدفوع</span>
                    @else
                        <span class="badge bg-danger">غير مدفوع</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

@endsection
