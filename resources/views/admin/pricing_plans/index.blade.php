@extends('layouts.app')

@section('content')

<div class="container py-4" style="direction: rtl; text-align:right;">

    <h3 class="mb-4 fw-bold">💰 جميع خطط التسعير</h3>

    @if(session('success'))
        <div class="alert alert-success text-center fw-bold">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.pricing_plans.create') }}" class="btn btn-primary mb-3">
        ➕ إضافة خطة جديدة
    </a>

    @if($plans->count() > 0)

    <div class="table-responsive">
        <table id="pricingTable" class="table table-bordered table-striped table-hover text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>النشاط</th>
                    <th>الفئة العمرية</th>
                    <th>الاسم</th>
                    <th>نوع التسعير</th>
                    <th>المدة</th>
                    <th>الحصص/أسبوع</th>
                    <th>الجنس</th>
                    <th>نوع العميل</th>
                    <th>السعر (دج)</th>
                    <th>الصلاحية</th>
                    <th>مفعل؟</th>
                    <th>إجراءات</th>
                </tr>
            </thead>

            <tbody>
                @foreach($plans as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->activity->title ?? '-' }}</td>
                    <td>{{ $p->ageCategory->name ?? '-' }}</td>
                    <td>{{ $p->name }}</td>

                    <td>
                        @switch($p->pricing_type)
                            @case('session') حصة @break
                            @case('weekly') أسبوعي @break
                            @case('monthly') شهري @break
                            @case('season') موسمي @break
                            @case('ticket') تذكرة @break
                            @default -
                        @endswitch
                    </td>

                    <td>
                        {{ $p->duration_value }}
                        @switch($p->duration_unit)
                            @case('day') يوم @break
                            @case('week') أسبوع @break
                            @case('month') شهر @break
                            @case('season') موسم @break
                            @default -
                        @endswitch
                    </td>

                    <td>{{ $p->sessions_per_week ?? '-' }}</td>

                    <td>
                        @switch($p->sexe)
                            @case('X') مختلط @break
                            @case('H') ذكور @break
                            @case('F') إناث @break
                            @default -
                        @endswitch
                    </td>

                    <td>
                        @switch($p->type_client)
                            @case('person') أفراد @break
                            @case('club') نادي @break
                            @case('company') شركة @break
                            @default -
                        @endswitch
                    </td>

                    <td>{{ number_format($p->price, 2) }}</td>

                    <td>
                        {{ $p->valid_from ?? '—' }} <br>
                        {{ $p->valid_to ?? '—' }}
                    </td>

                    <td>
                        @if($p->active)
                            <span class="badge bg-success">✔</span>
                        @else
                            <span class="badge bg-danger">✘</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('admin.pricing_plans.edit', $p->id) }}"
                           class="btn btn-sm btn-warning" title="تعديل">
                            ✏
                        </a>

                        <form action="{{ route('admin.pricing_plans.destroy', $p->id) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('هل تريد حذف هذه الخطة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                🗑
                            </button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>

        </table>
    </div>

    @else
    <div class="alert alert-warning text-center fw-bold">
        ⚠ لا توجد خطط تسعير حالياً
    </div>
    @endif

</div>

@endsection

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.min.css">
@endpush

@push('js')
@include('admin.partials.datatable-script', ['tableId' => '#pricingTable'])
@endpush


