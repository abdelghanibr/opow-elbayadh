@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="fw-bold mb-1">💾 النسخ الاحتياطية لقاعدة البيانات</h3>
            <p class="text-muted small mb-0">تُنشأ نسخة تلقائيًا عند كل دخول مسؤول · تُحذف النسخ الأقدم من {{ $days }} يومًا تلقائيًا</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.audit') }}" class="btn btn-outline-secondary">🕵️ سجل التتبع</a>
            <form method="POST" action="{{ route('admin.backups.run') }}">@csrf
                <button class="btn btn-primary">➕ نسخة احتياطية الآن</button></form>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="card border-0 shadow-sm"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead class="table-light"><tr><th>الملف</th><th>التاريخ</th><th>الحجم</th><th></th></tr></thead>
        <tbody>
        @forelse($files as $f)
            <tr>
                <td dir="ltr" class="text-end">{{ $f['name'] }}</td>
                <td>{{ date('d/m/Y H:i:s', $f['time']) }}</td>
                <td>{{ number_format($f['size']/1024, 0) }} KB</td>
                <td><a class="btn btn-sm btn-outline-success" href="{{ route('admin.backups.download', $f['name']) }}">⬇️ تحميل</a></td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted py-4">لا توجد نسخ بعد</td></tr>
        @endforelse
        </tbody>
    </table></div></div>
</div>
@endsection
