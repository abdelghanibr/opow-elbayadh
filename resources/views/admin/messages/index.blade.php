@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align: right;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">📩 رسائل الإدارة إلى الحسابات</h3>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">الرجوع</a>
    </div>

    @if(session('success'))<div class="alert alert-success fw-bold">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger fw-bold">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    @unless($ready)
        <div class="alert alert-warning">جدول الرسائل غير موجود بعد. نفّذ ملف <code>messages-clients-11-12-2026.sql</code> في قاعدة البيانات.</div>
    @else
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-bold">إرسال رسالة (إشعار يظهر في حساب العميل)</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.messages.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">البريد الإلكتروني للحساب (واحد أو عدة، مفصولة بفاصلة أو سطر جديد)</label>
                    <textarea name="emails" rows="3" class="form-control" dir="ltr" required placeholder="client@example.com">{{ old('emails') }}</textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">العنوان</label>
                        <input type="text" name="title" class="form-control" maxlength="255" value="{{ old('title') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">رقم الحجز (اختياري)</label>
                        <input type="number" name="reservation_id" class="form-control" min="1" value="{{ old('reservation_id') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">نص الرسالة</label>
                    <textarea name="body" rows="5" class="form-control" maxlength="5000" required>{{ old('body') }}</textarea>
                </div>
                <button class="btn btn-primary" onclick="return confirm('إرسال الرسالة إلى الحسابات المذكورة؟')">إرسال</button>
            </form>
        </div>
    </div>

    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="بحث بالبريد أو الاسم أو العنوان">
        <button class="btn btn-outline-primary">بحث</button>
    </form>

    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light"><tr><th>#</th><th>الحساب</th><th>الحجز</th><th>العنوان</th><th>الحالة</th><th>التاريخ</th><th></th></tr></thead>
            <tbody>
            @forelse($messages as $m)
                <tr>
                    <td>{{ $m->id }}</td>
                    <td dir="ltr" class="text-start">{{ $m->user_email }}<br><small class="text-muted">{{ $m->user_name }}</small></td>
                    <td>{{ $m->reservation_id ?: '—' }}</td>
                    <td title="{{ $m->body }}">{{ $m->title }}</td>
                    <td>
                        @if($m->read_at)
                            <span class="badge bg-success">مقروءة</span>
                        @else
                            <span class="badge bg-warning text-dark">غير مقروءة</span>
                        @endif
                    </td>
                    <td>{{ $m->created_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.messages.destroy', $m->id) }}" onsubmit="return confirm('حذف هذه الرسالة؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">لا توجد رسائل.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @endunless
</div>
@endsection
