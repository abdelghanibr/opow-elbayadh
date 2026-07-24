@extends('layouts.app')

@section('content')

<div class="container py-4" style="direction: rtl; text-align:right;">

    {{-- HEADER --}}
    <div class="p-3 mb-4"
         style="background: linear-gradient(to right, #0a4f88, #0a8a67);
                border-radius: 10px;
                color: #fff;
                font-weight:600;">
        <div class="d-flex justify-content-between align-items-center">
            <span>📅 إدارة الأفواج (Schedules)</span>

            <a href="{{ route('admin.schedules.create') }}" class="btn btn-light fw-bold">
                + إضافة فوج
            </a>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="card p-3 shadow-sm mb-3">
        <div class="row g-3">

            <div class="col-md-3">
                <label>المنشأة</label>
                <select id="filterComplex" class="form-control">
                    <option value="">الكل</option>
                    @foreach($complexes as $c)
                        <option value="{{ $c->nom }}">{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>النشاط</label>
                <select id="filterActivity" class="form-control">
                    <option value="">الكل</option>
                    @foreach($activities as $a)
                        <option value="{{ $a->title }}">{{ $a->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>الجنس</label>
                <select id="filterSex" class="form-control">
                    <option value="">الكل</option>
                    <option value="ذكور">ذكور</option>
                    <option value="إناث">إناث</option>
                    <option value="مختلط">مختلط</option>
                </select>
            </div>

        </div>
    </div>

    {{-- TABLE --}}
    <div class="card p-3 shadow-sm">

        <table id="schedulesTable" class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>المركب</th>
                    <th>النشاط</th>
                    <th>الفئة العمرية</th>
                    <th>المجموعة</th>
                    <th>الساعات المختارة</th>
                    <th>الجنس</th>
                    <th>العدد</th>
                    <th>عدد الحجوزات</th>
                    <th>السعر</th>
                    <th>مخصص لـ</th>
                    <th>نوع الاشتراك</th>
                    <th>الفترة</th>
                    <th>الحالة</th>
                    <th>التحكم</th>
                </tr>
            </thead>

            <tbody>
                @foreach($schedules as $s)

                <tr
                    data-complex="{{ $s->complexActivity->complex->nom ?? '' }}"
                    data-activity="{{ $s->complexActivity->activity->title ?? '' }}"
                    data-sex="{{ $s->sex == 'H' ? 'ذكور' : ($s->sex == 'F' ? 'إناث' : 'مختلط') }}"
                >
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->complexActivity->complex->nom ?? '—' }}</td>
                    <td>{{ $s->complexActivity->activity->title ?? '—' }}</td>
                    <td>{{ $s->ageCategory->name ?? '—' }}</td>
                    <td>{{ $s->groupe }}</td>

                    {{-- slots removed --}}
                {{-- 🟦 عرض time_slots --}}
                    <td style="text-align:right;">
                        @php
                            $slots = $s->time_slots;

                            if (is_string($slots)) {
                                $slots = json_decode($slots, true);
                            }
                            if (!is_array($slots)) {
                                $slots = [];
                            }

                            $daysMap = [
                                0 => "الأحد",
                                1 => "الإثنين",
                                2 => "الثلاثاء",
                                3 => "الأربعاء",
                                4 => "الخميس",
                                5 => "الجمعة",
                                6 => "السبت",
                            ];
                        @endphp

                        @forelse ($slots as $slot)
                            
                            
                            
        <div class="badge bg-secondary d-block mb-1 text-start">
            📅 {{ $daysMap[$slot['day_number']] ?? '—' }}<br>
            ⏱ {{ $slot['start'] ?? '?' }} → {{ $slot['end'] ?? '?' }}
        </div>
                        @empty
                            <span class="text-muted">لا توجد مواعيد</span>
                        @endforelse
                    </td>
                    <td>
                        @if($s->sex == 'H') ذكور
                        @elseif($s->sex == 'F') إناث
                        @else مختلط
                        @endif
                    </td>

                    <td>{{ $s->nbr ?? '—' }}</td>

                    <td>
                        @if(isset($scheduleSeasonCounts[$s->id]))
                            @foreach($scheduleSeasonCounts[$s->id] as $row)
                                <span class="badge bg-primary d-inline-block mb-1">
                                    {{ $row->season_name }} : {{ $row->total }}
                                </span><br>
                            @endforeach
                        @else
                            <span class="text-muted">0</span>
                        @endif
                    </td>

                    <td>
                        @if($s->type_prix == 'fix')
                            {{ $s->price }} دج
                        @else
                            —
                        @endif
                    </td>

                    <td>
                        @if($s->user_id)
                            {{ $s->user->name }}<br>
                            <small class="text-muted">({{ $s->user->type }})</small>
                        @else
                            —
                        @endif
                    </td>

                    <td>
                        @php
                            $labels = [
                                'session'=>'جلسة',
                                'weekly'=>'أسبوعي',
                                'monthly'=>'شهري',
                                'quarterly'=>'ثلاثي',
                                'semester'=>'سداسي',
                                'season'=>'موسمي',
                                'ticket'=>'تذكرة',
                            ];
                        @endphp
                        <span class="badge bg-info text-dark">
                            {{ $labels[$s->type_season] ?? '—' }}
                        </span>
                    </td>

                    <td>
                        <small>
                            {{ $s->date_debut?->format('Y-m-d') }}
                            →
                            {{ $s->date_fin?->format('Y-m-d') }}
                        </small>
                    </td>

                    <td>
                        @if($s->active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-secondary">غير نشط</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('admin.schedules.edit', $s->id) }}"
                           class="btn btn-warning btn-sm">✏ تعديل</a>

                        <form action="{{ route('admin.schedules.destroy', $s->id) }}"
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                🗑 حذف
                            </button>
                        </form>
                    </td>
                </tr>

                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection

@push('js')

@include('admin.partials.datatable-script', ['tableId' => '#schedulesTable'])

<script>
$(document).ready(function () {

    // ✅ استخدم نفس الـ instance التي أنشأها partial
    let table = $.fn.dataTable.isDataTable('#schedulesTable')
        ? $('#schedulesTable').DataTable()
        : $('#schedulesTable').DataTable();

    $('#filterComplex, #filterActivity, #filterSex').on('change', function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {

        let complex  = $('#filterComplex').val();
        let activity = $('#filterActivity').val();
        let sex      = $('#filterSex').val();

        let row = table.row(dataIndex).node();

        let rowComplex  = $(row).data('complex') || '';
        let rowActivity = $(row).data('activity') || '';
        let rowSex      = $(row).data('sex') || '';

        if (complex && rowComplex !== complex) return false;
        if (activity && rowActivity !== activity) return false;
        if (sex && rowSex !== sex) return false;

        return true;
    });
});

</script>

@endpush
