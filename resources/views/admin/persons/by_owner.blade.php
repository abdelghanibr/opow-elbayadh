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
                    <th>الصورة</th>
                    <th>الاسم</th>
                    <th>اللقب</th>
                    <th>تاريخ الميلاد</th>
                    <th>الجنس</th>
                    <th>التصنيف</th>
                    <th>رقم الإجازة</th>
                    <th>الاستمارة</th>
                </tr>
            </thead>

            <tbody>
            @foreach($persons as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($p->photo)
                            <img src="{{ asset($p->photo) }}"
                                 alt="photo"
                                 class="person-avatar"
                                 data-bs-toggle="modal"
                                 data-bs-target="#photoModal{{ $p->id }}">
                        @else
                            <img src="{{ asset('images/avatar-default.png') }}"
                                 class="person-avatar">
                        @endif
                    </td>
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
                    <td>{{ $p->study_level ?? '—' }}</td>
                    <td class="fw-bold text-success">
                        {{ $p->license_number ?? '—' }}
                    </td>
                    <td>
                        @if(!empty($p->birth_certificate))
                            <a href="{{ $p->birth_certificate }}" target="_blank"
                               class="btn btn-sm btn-outline-success rounded-pill"
                               title="عرض الإستمارة">
                                📄
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>

                @if($p->photo)
                <div class="modal fade" id="photoModal{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <img src="{{ asset($p->photo) }}"
                                     class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>
                @endif

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
.person-avatar {
    width: 45px;
    height: 45px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    transition: transform .2s ease;
}
.person-avatar:hover {
    transform: scale(1.08);
}
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
