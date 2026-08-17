@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align: right;">

    <h3 class="mb-4 fw-bold">🏊‍♂️ إدارة النوادي المسجلة</h3>

    {{-- ===== فلترة الحالة ===== --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <label class="form-label fw-bold">فلترة حسب الحالة</label>
            <select id="filterEtat" class="form-select form-select-sm">
                <option value="">كل الحالات</option>
                <option value="pending">⏳ قيد الدراسة</option>
                <option value="approved">✔ مقبول</option>
                <option value="rejected">❌ مرفوض</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">فلترة حسب المركب</label>
            <select id="filterComplex" class="form-select form-select-sm">
                <option value="">كل المركبات</option>
                @foreach($complexes as $complex)
                    <option value="{{ $complex->nom }}">{{ $complex->nom }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ===== الجدول ===== --}}
    <div class="table-responsive">
        <table id="clubsTable"
               class="table table-bordered table-striped table-hover text-center align-middle w-100">

            <thead class="table-dark">
                <tr>
                    <th data-priority="6">#</th>
                    <th data-priority="1">اسم النادي</th>
                    <th data-priority="2">المركب</th>
                    <th data-priority="9">المرفقات</th>
                    <th data-priority="3">إجراءات</th>
                    <th data-priority="4">حذف نهائي</th>
                    <th data-priority="10">رقم الإعتماد</th>
                    <th data-priority="11">تاريخ نهاية الإعتماد</th>
                    <th data-priority="5">الحالة</th>
                    <th data-priority="8">تاريخ التسجيل</th>
                    <th data-priority="12">تاريخ الموافقة</th>
                    <th data-priority="13">ملاحظة المسؤول</th>
                </tr>
            </thead>

            <tbody>
            @foreach($clubs as $c)

                @php
                    $files = json_decode($c->attachments, true) ?? [];

                    $labels = [
                        'agreement'              => '🏛️ اعتماد النادي',
                        'statut'                 => '📜 القانون الأساسي',
                        'bureau_members'         => '👥 أعضاء المكتب المسير',
                        'coaches_certificates'   => '🎓 شهادات المدربين',
                        'federation_affiliation' => '🏅 شهادة الانخراط في الرابطة',
                        'insurance_certificate'  => '🛡️ شهادة التأمين',
                        'rules_book'             => '📘 دفتر الشروط',
                        'minutes_meeting'        => '📝 محضر الجمعية العامة',
                        'exploitation_request'   => '📄 طلب الاستغلال',
                    ];
                @endphp

                <tr>
                    <td>{{ $c->id }}</td>
                    <td class="fw-semibold">{{ $c->nom }}</td>
                    <td>
                        <span class="complex d-none">{{ $c->user?->complex?->nom ?? '—' }}</span>
                        <span class="badge bg-info-subtle text-info-emphasis">{{ $c->user?->complex?->nom ?? '—' }}</span>
                    </td>

                    {{-- المرفقات --}}
                    <td class="text-start">
                        @if(count($files))
                            <div class="attachments-box">
                                @foreach($files as $key => $path)
                                    <div class="attachment-item">
                                        <span class="attachment-title">
                                            {{ $labels[$key] ?? '📎 وثيقة' }}
                                        </span>
                                        <a href="{{ asset($path) }}"
                                           target="_blank"
                                           class="btn btn-outline-primary btn-xs">
                                            ⬇ تحميل
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            —
                        @endif
                    </td>

                    {{-- الإجراءات --}}
                    <td style="white-space: nowrap;">
                        @if($c->etat === 'pending')
                            <a href="{{ route('admin.clubs.approve', $c->id) }}"
                               class="btn btn-success btn-sm"
                               onclick="return confirm('قبول النادي؟')">
                               ✔ قبول
                            </a>

                            <a href="{{ route('admin.clubs.reject', $c->id) }}"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('رفض النادي؟')">
                               ❌ رفض
                            </a>

                            <button class="btn btn-secondary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#noteModal{{ $c->id }}">
                                📝 ملاحظة
                            </button>
                        @else
                            —
                        @endif
  <a href="{{ route('persons.byOwner', $c->user_id) }}" class="btn btn-primary btn-sm">
    👥 عرض  رياضيين النادي
</a>

                    </td>

                    {{-- 🗑️ حذف نهائي (عمود منفصل + تأكيد محمي) --}}
                    <td style="white-space: nowrap;">
                        <button class="btn btn-outline-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteClubModal{{ $c->id }}">
                            🗑️ حذف
                        </button>
                    </td>

                    <td>{{ $c->numero_agrement }}</td>
                    <td>{{ $c->date_expiration }}</td>

                          {{-- الحالة --}}
                    <td>
                        <span class="etat d-none">{{ $c->etat }}</span>

                        @if($c->etat === 'pending')
                            <span class="badge bg-warning text-dark">⏳ قيد الدراسة</span>
                        @elseif($c->etat === 'approved')
                            <span class="badge bg-success">✔ مقبول</span>
                        @else
                            <span class="badge bg-danger">❌ مرفوض</span>
                        @endif
                    </td>

                     <td>{{ $c->created_at }}</td>
                    <td>
@if($c->validated_at) 
    <small class="text-muted">
        @if($c->etat === 'approved') 
            ✔️ <strong>مقبول</strong>
        @elseif($c->etat === 'rejected') 
            ❌ <strong>مرفوض</strong>
        @else
            🕒 <strong>قيد المعالجة</strong>
        @endif
        <br>
        بتاريخ {{ $c->validated_at}} 
        <br>
        بواسطة {{ $c->validator->name ?? '—' }} 
    </small>
@else
    <span class="badge bg-secondary">لم يُراجع بعد</span>
@endif
</td>
             

                    {{-- الملاحظة --}}
                    <td class="text-start small">
                        {{ $c->note_admin ?? '—' }}
                    </td>
                </tr>

                {{-- ===== Modal حذف نهائي (يتطلب كتابة اسم النادي) ===== --}}
                <div class="modal fade" id="deleteClubModal{{ $c->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('admin.clubs.destroy', $c->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">🗑️ حذف نهائي</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="alert alert-danger small">
                                        سيتم حذف النادي <strong>{{ $c->nom }}</strong>
                                        وحساب تسجيل الدخول الخاص به نهائيًا.<br>
                                        هذه العملية لا يمكن التراجع عنها.
                                    </div>

                                    <label class="fw-bold small">
                                        اكتب اسم النادي لتأكيد الحذف:
                                    </label>
                                    <input type="text"
                                           class="form-control mt-1 delete-club-confirm-input"
                                           data-club-name="{{ $c->nom }}"
                                           data-confirm-btn="#btnConfirmDelete{{ $c->id }}"
                                           placeholder="{{ $c->nom }}"
                                           autocomplete="off">
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit"
                                            id="btnConfirmDelete{{ $c->id }}"
                                            class="btn btn-danger"
                                            disabled>
                                        حذف نهائي
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ===== Modal Note Admin ===== --}}
                <div class="modal fade" id="noteModal{{ $c->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <form action="{{ route('admin.clubs.note', $c->id) }}" method="POST">
                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">📝 ملاحظة المسؤول</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <textarea name="note_admin"
                                              class="form-control form-control-sm"
                                              rows="4"
                                              placeholder="اكتب ملاحظتك هنا...">{{ $c->note_admin }}</textarea>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success btn-sm">💾 حفظ</button>
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                        إلغاء
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.min.css">

<style>
table.dataTable {
    font-size: 12px;
}

.btn {
    white-space: nowrap;
}

table thead th {
    white-space: nowrap;
}

.attachments-box {
    background: #f8fafc;
    padding: 8px;
    border-radius: 10px;
}

.attachment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 8px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    margin-bottom: 5px;
}

.attachment-title {
    font-size: 12px;
    font-weight: 600;
}

.btn-xs {
    font-size: 11px;
    padding: 3px 8px;
}
</style>
@endpush




