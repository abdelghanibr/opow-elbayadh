@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="direction: rtl; text-align: right;">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0">🚫 الحسابات بدون ملف</h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                🗑️ حذف الكل ({{ $orphans->count() }})
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">⬅ رجوع</a>
        </div>
    </div>

    @if($orphans->isEmpty())
        <div class="alert alert-success">
            ✔ لا توجد حسابات بدون ملف{{ isset($scopeComplex) && $scopeComplex ? ' في مجمع «' . $scopeComplex->nom . '»' : '' }}. جميع الحسابات قدّمت ملفًا.
        </div>
    @else
        <div class="alert alert-warning small">
            @if(isset($scopeComplex) && $scopeComplex)
                <strong>نطاق العرض: مجمع «{{ $scopeComplex->nom }}»</strong> — يُعرض فقط المنخرطون/النوادي/المؤسسات المسجلة في هذا المجمع.
                <hr>
            @endif
            هذه الحسابات (منخرط / نادي / مؤسسة) لم تُقدّم <strong>أي ملف</strong> على الإطلاق.
            حذف الحساب يزيل نهائيًا: الحساب، أشخاصه (وأبنائهم)، ملفاته، حجوزاته ومدفوعاته.
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table id="orphansTable" class="table table-bordered table-striped table-hover text-center align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الإيمايل</th>
                            <th>الهاتف</th>
                            <th>النوع</th>
                            <th>تاريخ التسجيل</th>
                            <th>حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($orphans as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td class="small">{{ $u->email }}</td>
                            <td class="small">{{ $u->phone ?? '—' }}</td>
                            <td>
                                @if($u->type === 'person')
                                    <span class="badge bg-primary">منخرط</span>
                                @elseif($u->type === 'club')
                                    <span class="badge bg-success">نادي</span>
                                @else
                                    <span class="badge bg-info text-dark">مؤسسة</span>
                                @endif
                            </td>
                            <td class="small">{{ $u->created_at }}</td>
                            <td>
                                <form action="{{ route('admin.accounts.destroy', $u->id) }}" method="POST"
                                      onsubmit="return confirm('هل تريد حذف الحساب «{{ $u->email }}» نهائيًا؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">🗑️ حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- ===== Modal حذف الكل (يتطلب كتابة «حذف الكل») ===== --}}
@if($orphans->isNotEmpty())
<div class="modal fade" id="deleteAllModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.accounts.destroy-all') }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">🗑️ حذف كل الحسابات بدون ملف</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger small">
                        سيتم حذف <strong>{{ $orphans->count() }} حسابًا</strong> نهائيًا (مع أشخاصها وملفاتها وحجوزاتها).
                        هذه العملية لا يمكن التراجع عنها.
                    </div>

                    <label class="fw-bold small">اكتب «حذف الكل» لتأكيد الحذف الجماعي:</label>
                    <input type="text" id="deleteAllConfirmInput" class="form-control mt-1"
                           placeholder="حذف الكل" autocomplete="off">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" id="btnDeleteAll" class="btn btn-danger" disabled>حذف الكل نهائيًا</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('js')
@include('admin.partials.datatable-script', ['tableId' => '#orphansTable'])

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('deleteAllConfirmInput');
    const btn = document.getElementById('btnDeleteAll');
    const modal = document.getElementById('deleteAllModal');

    if (input && btn) {
        input.addEventListener('input', function () {
            btn.disabled = input.value.trim() !== 'حذف الكل';
        });

        modal.addEventListener('hidden.bs.modal', function () {
            input.value = '';
            btn.disabled = true;
        });
    }
});
</script>
@endpush
