@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="direction: rtl; text-align:right;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="fw-bold mb-1">🕵️ سجل تتبع المستخدمين</h3>
            <p class="text-muted small mb-0">من قام بماذا ومتى — كل عملية حذف تحتفظ بنسخة كاملة من البيانات المحذوفة</p>
        </div>
        <a href="{{ route('admin.backups') }}" class="btn btn-outline-primary">💾 النسخ الاحتياطية</a>
    </div>

    <form method="GET" class="card card-body mb-3 border-0 shadow-sm">
        <div class="row g-2 align-items-end">
            <div class="col-md-2"><label class="small">المستخدم</label><input name="user" value="{{ request('user') }}" class="form-control form-control-sm"></div>
            <div class="col-md-2"><label class="small">العملية</label>
                <select name="event" class="form-select form-select-sm"><option value="">الكل</option>
                    @foreach($events as $e)<option value="{{ $e }}" @selected(request('event')===$e)>{{ $e }}</option>@endforeach
                </select></div>
            <div class="col-md-2"><label class="small">الجدول / النموذج</label><input name="model" value="{{ request('model') }}" placeholder="Person" class="form-control form-control-sm"></div>
            <div class="col-md-1"><label class="small">رقم السجل</label><input name="record" value="{{ request('record') }}" class="form-control form-control-sm"></div>
            <div class="col-md-2"><label class="small">من</label><input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm"></div>
            <div class="col-md-2"><label class="small">إلى</label><input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm"></div>
            <div class="col-md-1"><button class="btn btn-primary btn-sm w-100">بحث</button></div>
            <div class="col-12"><label class="small"><input type="checkbox" name="with_requests" value="1" @checked(request('with_requests'))> إظهار طلبات الكتابة (POST/PUT/DELETE) أيضًا</label></div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="font-size:.85rem">
            <thead class="table-light"><tr>
                <th>#</th><th>التاريخ</th><th>المستخدم</th><th>العملية</th><th>الجدول</th><th>السجل</th><th>التفاصيل</th><th>IP</th>
            </tr></thead>
            <tbody>
            @forelse($logs as $l)
                @php $cls = ['deleted'=>'danger','sql_delete'=>'danger','login_failed'=>'warning','created'=>'success','updated'=>'primary','backup'=>'info','backup_failed'=>'danger'][$l->event] ?? 'secondary'; @endphp
                <tr>
                    <td class="text-muted">{{ $l->id }}</td>
                    <td style="white-space:nowrap">{{ $l->created_at?->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $l->user_name ?? '—' }} <span class="text-muted small">{{ $l->user_type }} @if($l->user_id)#{{ $l->user_id }}@endif</span></td>
                    <td><span class="badge bg-{{ $cls }}">{{ $l->event }}</span></td>
                    <td>{{ $l->auditable_type ? class_basename($l->auditable_type) : ($l->route ?? '') }}</td>
                    <td>{{ $l->auditable_id }}</td>
                    <td style="max-width:520px">
                        @if($l->old_values || $l->new_values || $l->url)
                        <details><summary class="small text-primary" style="cursor:pointer">عرض</summary>
                            @if($l->url)<div class="small text-muted" dir="ltr">{{ $l->method }} {{ $l->url }}</div>@endif
                            @if($l->old_values)<div class="small fw-bold mt-1">قبل:</div><pre dir="ltr" class="small mb-1" style="white-space:pre-wrap">{{ json_encode($l->old_values, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>@endif
                            @if($l->new_values)<div class="small fw-bold">بعد:</div><pre dir="ltr" class="small mb-0" style="white-space:pre-wrap">{{ json_encode($l->new_values, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>@endif
                        </details>
                        @endif
                    </td>
                    <td class="small text-muted" dir="ltr">{{ $l->ip }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">لا توجد سجلات</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
    <div class="mt-3">{{ $logs->links() }}</div>
</div>
@endsection
