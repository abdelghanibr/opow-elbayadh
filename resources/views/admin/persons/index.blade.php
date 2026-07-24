@extends('layouts.app')

@section('content')
<div class="container-fluid" style="direction: rtl">

    <div class="d-flex justify-content-between align-items-center mb-3">
       <h4 class="fw-bold mb-0">👥 قائمة المنخرطين</h4 

        <!-- زر الطباعة كما هو من الكود الأصلي -->
        <button id="printSelected" class="btn btn-primary">
            🖨 طباعة المختارين
        </button>
    </div>

    <!-- 🔍 فلاتر أعلى الجدول (فقط حالة الملف + حالة التأمين) -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">

                <!-- فلتر حالة التأمين etat_ass -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">🔐 حالة التأمين</label>
                    <select id="filterAssurance" class="form-select">
                        <option value="">الكل</option>
                        <option value="1">مؤمَّن</option>
                        <option value="0">غير مؤمَّن</option>
                    </select>
                </div>

                <!-- فلتر حالة الملف dossier.etat -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">📂 حالة الملف</label>
                    <select id="filterDossier" class="form-select">
                        <option value="">الكل</option>
                        <option value="approved">مقبول</option>
                        <option value="pending">قيد المراجعة</option>
                        <option value="rejected">مرفوض</option>
                        <option value="none">غير موجود</option>
                    </select>
                </div>
                
                 <div class="col-md-4">
                <label class="form-label fw-bold">🏟️ المنشأة</label>
                <select id="filterComplex" class="form-select">
                    <option value="">الكل</option>
                    @foreach($complexes as $c)
                        <option value="{{ $c->id }}">{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
          <div class="col-md-4">
    <label class="form-label fw-bold">👶 الفئة العمرية</label>
<select id="filterAgeCategory" class="form-select">
    <option value="">الكل</option>
    @foreach($ageCategories as $cat)
        <option value="{{ $cat->name }}">
            {{ $cat->name }}
        </option>
    @endforeach
</select>



</div>

            </div>
        </div>
    </div>

<!-- 📊 Section Statistiques -->
<div class="row mb-3" id="statsSection">

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="fw-bold mb-1">👨 عدد الذكور</h6>
                <h3 class="text-primary mb-0" id="statMale">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="fw-bold mb-1">👩 عدد الإناث</h6>
                <h3 class="text-danger mb-0" id="statFemale">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="fw-bold mb-1">👥 المجموع</h6>
                <h3 class="text-success mb-0" id="statTotal">0</h3>
            </div>
        </div>
    </div>

</div>



    <div class="card shadow-sm">
        <div class="card-body">

            <table id="capacityTable" class="table table-bordered table-hover align-middle text-center w-100">

                <thead class="table-dark">
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>#</th>
                    <th>الاسم الكامل</th>
                    <th>الجنس</th>
                   <th>تاريخ التسجيل</th>
                    <th>العمر</th>
                    <th>الهاتف</th>
                    <th>الفئة العمرية</th>
                     <th>الإيمايل</th>
                    <th>المنشأة</th>
                    <th>حالة الدفع</th>
                    <th>حالة التأمين</th>
                                   <th>الفريق</th>
                    <th>حذف</th>
                </tr>
                </thead>

                <tbody>
                @foreach($persons as $p)
                    @php
                        $etat = optional($p->dossier)->etat; // 'approved'/'pending'/'rejected'/null
                        $etatDossierKey = $etat ?? 'none';
                    @endphp
                    <tr
                        data-ass="{{ (int) $p->etat_ass }}"
                        data-dossier="{{ $etatDossierKey }}"
                        data-gender="{{ strtoupper(trim($p->gender)) }}"
                          data-complex="{{ $p->user->complex->id ?? '' }}"
                         
                         data-age-category="{{ $p->ageCategory->name ?? '' }}"

                    >

                        <!-- Checkbox -->
                        <td>
                            <input type="checkbox" class="row-check" value="{{ $p->id }}">
                        </td>

                        <!-- ID -->
                        <td>{{ $p->id }}</td>

                        <!-- Name -->
                        <td>{{ $p->firstname }} {{ $p->lastname }}</td>

                        <!-- Gender -->
                        <td>{{ $p->gender }}</td>

                        <!-- Birth -->
                        <td>{{ $p->created_at }}</td>

                        <!-- Age -->
                        <td>{{ \Carbon\Carbon::parse($p->birth_date)->age }}</td>

                        <!-- Phone -->
                        <td>{{ $p->phone }}</td>

                        <!-- Age Category -->
                        <td>{{ $p->ageCategory->name ?? '—' }}</td>

                        <!-- City -->
                        <td>{{ $p->user->email ?? '—' }}</td>

                        <!-- Complex name -->
                        <td>{{ $p->user->complex->nom ?? '—' }}</td>

                        <!-- Dossier Status (badge) -->
                        <td>
@php
    $paymentStatus = optional(optional($p->user)->latestReservation)->payment_status;
@endphp

@if($paymentStatus === 'paid')
    <span class="badge bg-success">مدفوع</span>
@elseif($paymentStatus === 'pending')
    <span class="badge bg-warning text-dark">جاري الدفع</span>
@elseif($paymentStatus === 'failed')
    <span class="badge bg-danger">فشل الدفع</span>
@else
    <span class="badge bg-secondary">لا توجد حجوزات</span>
@endif
                        </td>

                        <!-- Assurance status (badge) -->
                        <td>
                            @if($p->etat_ass == 1)
                                <span class="badge bg-success">مؤمَّن</span>
                            @else
                                <span class="badge bg-danger">غير مؤمَّن</span>
                            @endif
                        </td>
    <td>{{ $p->user->name ?? '—' }}</td>
                        <!-- Delete Action -->
                        <td>
                            <form action="{{ route('persons.destroy', $p->id) }}" method="POST"
                                  onsubmit="return confirm('هل تريد حذف هذا الشخص؟')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">🗑</button>
                            </form>
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection

@push('js')
@include('admin.partials.datatable-script', ['tableId' => '#capacityTable'])
<script>
let table;

$(document).ready(function () {

    table = $('#capacityTable').DataTable();
    updateStats();

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {

        let assFilter     = $('#filterAssurance').val();
        let dossierFilter = $('#filterDossier').val();
        let complexFilter = $('#filterComplex').val();
        let ageCatFilter  = $('#filterAgeCategory').val(); // 👈 الجديد

        let rowNode = table.row(dataIndex).node();
        let $row    = $(rowNode);

        let rowAss     = ($row.data('ass') ?? '').toString();
        let rowDoss    = ($row.data('dossier') ?? '').toString();
        let rowComplex = ($row.data('complex') ?? '').toString();
        let rowAgeCat  = ($row.data('age-category') ?? '').toString(); // 👈 المهم

        if (assFilter !== '' && rowAss !== assFilter) return false;
        if (dossierFilter !== '' && rowDoss !== dossierFilter) return false;
        if (complexFilter !== '' && rowComplex !== complexFilter) return false;

        // 👶 فلترة حسب فئة العمر (بالاسم)
        if (ageCatFilter !== '' && rowAgeCat !== ageCatFilter) return false;

        return true;
    });

    $('#filterAssurance, #filterDossier, #filterComplex, #filterAgeCategory')
        .on('change', function () {
            table.draw();
        });

    table.on('draw', function () {
        updateStats();
    });
    
      // 🔁 عند أي redraw (بحث – pagination – فلترة)
        table.on('draw', function () {
            updateStats();
        });

        // ✅ تحديد الكل
        $('#selectAll').on('click', function () {
            $('.row-check').prop('checked', this.checked);
        });

        // ✅ زر الطباعة
        $('#printSelected').on('click', function () {

            let selected = [];
            $('.row-check:checked').each(function () {
                selected.push($(this).val());
            });

            if (selected.length === 0) {
                alert("الرجاء اختيار شخص واحد على الأقل");
                return;
            }

            window.open("/print/persons?ids=" + selected.join(','), "_blank");

            fetch("/update-assurance", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ ids: selected })
            });
        });


});

function updateStats() {

    let male = 0, female = 0, total = 0;

    table.rows({ filter: 'applied' }).every(function () {

        let row = $(this.node());
        let gender = (row.data('gender') || '').toString().trim();

        if (gender === 'H') male++;
        else if (gender === 'F') female++;

        total++;
    });

    $('#statMale').text(male);
    $('#statFemale').text(female);
    $('#statTotal').text(total);
}
</script>



@endpush
