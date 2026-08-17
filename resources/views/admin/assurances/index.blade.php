@extends('layouts.app')

@section('content')
<style>
    .badge-ass { padding: 4px 10px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
    .badge-ass.pending { background: #fff3cd; color: #856404; }
    .badge-ass.assured { background: #d1e7dd; color: #0f5132; }
    .badge-ass.expired  { background: #f8d7da; color: #842029; }
    .badge-ass.cancelled { background: #e2e3e5; color: #41464b; }
    .stat-card { border-radius: 16px; padding: 18px 22px; color: #fff; min-width: 140px; }
    .stat-card h3 { margin: 0; font-size: 1.6rem; }
    .stat-card small { opacity: .85; }
    .ass-mini-btn { border: none; background: #e9ecef; padding: 3px 10px; border-radius: 8px; font-size: .75rem; }
</style>

<div class="container-fluid py-4" style="direction:rtl;text-align:right;">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0">🛡️ إدارة التأمينات</h4>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">⬅ رجوع</a>
    </div>

    {{-- ===== Stats ===== --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#0d6efd,#0a58ca)">
            <h3>{{ $stats['total'] }}</h3><small>الإجمالي</small>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#ffc107,#e0a800)">
            <h3>{{ $stats['pending'] }}</h3><small>معني بالتأمين</small>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#198754,#157347)">
            <h3>{{ $stats['assured'] }}</h3><small>مؤمن</small>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#dc3545,#bb2d3b)">
            <h3>{{ $stats['expired'] }}</h3><small>منتهي</small>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#6c757d,#5a6268)">
            <h3>{{ $stats['cancelled'] }}</h3><small>ملغى</small>
        </div>
    </div>

    {{-- ===== Filtres ===== --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">المجمع</label>
                    <select id="filterComplex" class="form-select form-select-sm">
                        <option value="all">الكل</option>
                        @foreach($complexes as $c)
                            <option value="{{ $c->id }}" {{ (int)$assignedComplexId === (int)$c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">الحالة</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="all">الكل</option>
                        <option value="pending">معني بالتأمين</option>
                        <option value="assured">مؤمن</option>
                        <option value="expired">منتهي</option>
                        <option value="cancelled">ملغى</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">نوع العملية</label>
                    <select id="filterOperation" class="form-select form-select-sm">
                        <option value="all">الكل</option>
                        <option value="new">تسجيل جديد</option>
                        <option value="renewal">تجديد</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">من تاريخ</label>
                    <input type="date" id="filterStartDate" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">إلى تاريخ</label>
                    <input type="date" id="filterEndDate" class="form-control form-control-sm">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" id="btnResetFilters" class="btn btn-outline-secondary btn-sm w-100">🔄</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Tabs ===== --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tabAssurances">🛡️ سجلات التأمين</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabCandidates">📋 مرشحون للتأمين</a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ===== Tab 1: Assurances ===== --}}
        <div class="tab-pane fade show active" id="tabAssurances">
            <div class="d-flex gap-2 mb-2">
                <button type="button" id="btnBulkAssure" class="btn btn-success btn-sm" disabled>✅ تأمين المحدد</button>
                <button type="button" id="btnPrintSelected" class="btn btn-dark btn-sm" disabled>🖨️ طباعة المحدد</button>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="assurancesTable" class="table table-bordered table-striped table-hover text-center align-middle w-100" style="font-size:.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th><input type="checkbox" id="checkAllAssurances"></th>
                                <th>#</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الاسم</th>
                                <th>اللقب</th>
                                <th>الهاتف</th>
                                <th>تاريخ الميلاد</th>
                                <th>المجمع</th>
                                <th>فترة الحجز</th>
                                <th>فترة التأمين</th>
                                <th>الحالة</th>
                                <th>النوع</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===== Tab 2: Candidates ===== --}}
        <div class="tab-pane fade" id="tabCandidates">
            <div class="d-flex gap-2 mb-2">
                <button type="button" id="btnStoreSelected" class="btn btn-primary btn-sm" disabled>➕ إضافة المحدد كتأمين</button>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="candidatesTable" class="table table-bordered table-striped table-hover text-center align-middle w-100" style="font-size:.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th><input type="checkbox" id="checkAllCandidates"></th>
                                <th>الاسم</th>
                                <th>اللقب</th>
                                <th>الهاتف</th>
                                <th>تاريخ الميلاد</th>
                                <th>المجمع</th>
                                <th>فترة الحجز</th>
                                <th>النوع</th>
                                <th>تاريخ الحجز</th>
                                <th># حجز</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.min.css">
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>

<script>
$(function () {
    var csrfToken = '{{ csrf_token() }}';
    var assurancesUrl = '{{ route("admin.assurances.data") }}';
    var candidatesUrl = '{{ route("admin.assurances.candidates-data") }}';
    var storeSelectedUrl = '{{ route("admin.assurances.store-selected") }}';
    var bulkAssureUrl = '{{ route("admin.assurances.bulk-assure") }}';
    var printSelectedUrl = '{{ route("admin.assurances.print-selected") }}';

    function getFilters() {
        return {
            complex_id: $('#filterComplex').val(),
            status: $('#filterStatus').val(),
            operation_type: $('#filterOperation').val(),
            start_date: $('#filterStartDate').val(),
            end_date: $('#filterEndDate').val()
        };
    }

    // ===== Assurances DataTable =====
    var tableAssurances = $('#assurancesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: assurancesUrl,
            data: function (d) { $.extend(d, getFilters()); }
        },
        language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' },
        pageLength: 25,
        order: [[1, 'desc']],
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'id' },
            { data: 'created_at' },
            { data: 'firstname' },
            { data: 'lastname' },
            { data: 'phone' },
            { data: 'birth_date' },
            { data: 'complex' },
            { data: 'reservation_period' },
            { data: 'assurance_period' },
            { data: 'status' },
            { data: 'operation_type' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        columnDefs: [{ targets: '_all', className: 'align-middle' }],
        dom: "<'row mb-2'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>><'row'<'col-12'tr>><'row mt-2'<'col-md-6'i><'col-md-6'p>>",
        buttons: [
            { extend: 'excelHtml5', text: '📊 Excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdfHtml5', text: '📄 PDF', className: 'btn btn-danger btn-sm' },
            { extend: 'print', text: '🖨 طباعة', className: 'btn btn-dark btn-sm' }
        ]
    });

    // ===== Candidates DataTable =====
    var tableCandidates = $('#candidatesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: candidatesUrl,
            data: function (d) { $.extend(d, getFilters()); }
        },
        language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' },
        pageLength: 10,
        order: [[8, 'desc']],
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'firstname' },
            { data: 'lastname' },
            { data: 'phone' },
            { data: 'birth_date' },
            { data: 'complex' },
            { data: 'reservation_period' },
            { data: 'operation_type' },
            { data: 'reservation_created_at' },
            { data: 'reservation_id' }
        ],
        columnDefs: [{ targets: '_all', className: 'align-middle' }],
        dom: "<'row mb-2'<'col-md-6'l><'col-md-6'f>><'row'<'col-12'tr>><'row mt-2'<'col-md-6'i><'col-md-6'p>>"
    });

    // ===== Filters =====
    $('#filterComplex, #filterStatus, #filterOperation, #filterStartDate, #filterEndDate').on('change', function () {
        tableAssurances.ajax.reload();
        tableCandidates.ajax.reload();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filterComplex').val('all');
        $('#filterStatus').val('all');
        $('#filterOperation').val('all');
        $('#filterStartDate').val('');
        $('#filterEndDate').val('');
        tableAssurances.ajax.reload();
        tableCandidates.ajax.reload();
    });

    // ===== Checkboxes & Bulk =====
    function getSelectedIds(selector) {
        var ids = [];
        $(selector + ':checked').each(function () { ids.push($(this).val()); });
        return ids;
    }

    $('#checkAllAssurances').on('change', function () {
        $('.assurance-check').prop('checked', this.checked).trigger('change');
    });

    $('#assurancesTable').on('change', '.assurance-check', function () {
        var total = $('.assurance-check').length;
        var checked = $('.assurance-check:checked').length;
        $('#checkAllAssurances').prop('checked', total > 0 && total === checked);
        $('#btnBulkAssure').prop('disabled', checked === 0);
        $('#btnPrintSelected').prop('disabled', checked === 0);
    });

    $('#checkAllCandidates').on('change', function () {
        $('.candidate-check').prop('checked', this.checked).trigger('change');
    });

    $('#candidatesTable').on('change', '.candidate-check', function () {
        var total = $('.candidate-check').length;
        var checked = $('.candidate-check:checked').length;
        $('#checkAllCandidates').prop('checked', total > 0 && total === checked);
        $('#btnStoreSelected').prop('disabled', checked === 0);
    });

    // ===== Bulk Assure =====
    $('#btnBulkAssure').on('click', function () {
        var ids = getSelectedIds('.assurance-check');
        if (ids.length === 0) return;
        if (!confirm('تأكيد تأمين ' + ids.length + ' سجل(ات)؟')) return;

        $.post(bulkAssureUrl, { ids: ids, _token: csrfToken }, function () {
            tableAssurances.ajax.reload();
            $('#btnBulkAssure, #btnPrintSelected').prop('disabled', true);
        }).fail(function (xhr) {
            alert('حدث خطأ: ' + (xhr.responseJSON?.message || 'غير معروف'));
        });
    });

    // ===== Print Selected =====
    $('#btnPrintSelected').on('click', function () {
        var ids = getSelectedIds('.assurance-check');
        if (ids.length === 0) return;

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = printSelectedUrl;
        form.target = '_blank';

        var inputToken = document.createElement('input');
        inputToken.type = 'hidden'; inputToken.name = '_token'; inputToken.value = csrfToken;
        form.appendChild(inputToken);

        ids.forEach(function (id) {
            var input = document.createElement('input');
            input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });

    // ===== Store Selected (Candidates → Assurances) =====
    $('#btnStoreSelected').on('click', function () {
        var ids = getSelectedIds('.candidate-check');
        if (ids.length === 0) return;
        if (!confirm('تأكيد إضافة ' + ids.length + ' سجل تأمين من المرشحين؟')) return;

        $.post(storeSelectedUrl, { reservation_ids: ids, _token: csrfToken }, function () {
            tableAssurances.ajax.reload();
            tableCandidates.ajax.reload();
            $('#btnStoreSelected').prop('disabled', true);
            location.reload();
        }).fail(function (xhr) {
            alert('حدث خطأ: ' + (xhr.responseJSON?.message || 'غير معروف'));
        });
    });
});
</script>
@endpush
