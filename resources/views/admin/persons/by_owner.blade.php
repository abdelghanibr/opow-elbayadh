@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">

    {{-- ===== العنوان ===== --}}
    <h3 class="fw-bold mb-4">👥 الأشخاص التابعون لـ: 
        <span class="text-primary">{{ $owner->name }}</span>
    </h3>

    {{-- زر رجوع --}}
    <div class="mb-3 text-end">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            ⬅ رجوع
        </a>
    </div>

    {{-- عدد الأشخاص --}}
    <div class="alert alert-info fw-bold">
        📌 عدد الأشخاص: {{ count($persons) }}
    </div>

    {{-- ===== الجدول ===== --}}
    <div class="table-responsive">
        <table id="personsTable" class="table table-bordered table-striped table-hover text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>اللقب</th>
                    <th>تاريخ الميلاد</th>
                    <th>الجنس</th>
                    <th>الفئة العمرية</th>
                    <th>رقم الإجازة</th>
                </tr>
            </thead>

            <tbody>
            @foreach($persons as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $p->firstname }}</td>
                    <td>{{ $p->lastname }}</td>
                    <td>{{ $p->birth_date }}</td>
                    <td>
                        @if($p->gender === 'ذكر')
                            <span class="badge bg-primary">ذكر</span>
                        @else
                            <span class="badge bg-danger">أنثى</span>
                        @endif
                    </td>
                    <td>{{ $p->ageCategory->name ?? '—' }}</td>
                    <td class="fw-bold text-success">
                        {{ $p->license_number ?? '—' }}
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>
    </div>
</div>
@endsection


{{-- ===== CSS ===== --}}
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.min.css">

<style>
table.dataTable { font-size: 13px; }
table thead th { white-space: nowrap; }
</style>
@endpush


{{-- ===== JS ===== --}}
@push('js')
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>

<!-- Buttons -->
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>

<!-- Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    new DataTable('#personsTable', {
        paging: true,
        searching: true,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '📊 Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdfHtml5',
                text: '📄 PDF',
                className: 'btn btn-danger btn-sm',
                orientation: 'landscape',
                pageSize: 'A4',
                customize: function(doc) {
                    doc.defaultStyle.fontSize = 10;
                }
            },
            {
                extend: 'print',
                text: '🖨 طباعة',
                className: 'btn btn-secondary btn-sm'
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json"
        }
    });
</script>
@endpush
