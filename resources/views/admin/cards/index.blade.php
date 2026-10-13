@extends('layouts.app')

@section('content')
<div class="container-fluid" style="direction: rtl">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">🪪 اختيار البطاقات المطلوب طباعتها</h4>
        <div>
            <button id="printCards" class="btn btn-success">
                🖨️ طباعة البطاقات المختارة
            </button>
            @if($persons->count())
            <a href="{{ route('admin.cards.print', ['ids' => $persons->implode('id', ',')]) }}" target="_blank"
               class="btn btn-outline-success" onclick="return confirm('سيتم طباعة جميع {{ $persons->count() }} بطاقة. متابعة؟')">
                طباعة الكل ({{ $persons->count() }})
            </a>
            @endif
        </div>
    </div>

    {{-- 📋 ملاحظة: الأهلية للطباعة --}}
    <div class="alert alert-info border-start border-4 border-info mb-3" style="direction: rtl">
        <strong>📌 ملاحظة:</strong> تُعرض في هذه القائمة فقط المنخرطون المؤهلون للطباعة:
        <ul class="mb-0 mt-1">
            <li>🏃 <strong>فرد:</strong> ملفه الشخصي <strong>معتمد</strong> في جدول الملفات.</li>
            <li>🏊 <strong>نادٍ / 🏢 مؤسسة:</strong> النادي / المؤسسة <strong>معتمد</strong> في جدول الأندية.</li>
        </ul>
        الأشخاص غير المعتمدين لا يمكن طباعة بطاقاتهم.
    </div>

    {{-- 🔍 فلاتر البحث --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.cards') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-bold">🔎 بحث (الاسم / رقم الإجازة)</label>
                    <input type="text" name="q" value="{{ $search }}"
                           class="form-control" placeholder="بحث بالاسم الكامل أو رقم الإجازة...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">🧭 النوع</label>
                    <select name="type" class="form-select">
                        <option value="">الكل</option>
                        <option value="person"  @selected($typeFilter === 'person')>🏃 فرد</option>
                        <option value="club"    @selected($typeFilter === 'club')>🏊 نادٍ</option>
                        <option value="company" @selected($typeFilter === 'company')>🏢 مؤسسة</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">🏟️ النادي / المؤسسة</label>
                    <select name="club" class="form-select">
                        <option value="">الكل</option>
                        @foreach($clubs as $c)
                            <option value="{{ $c->id }}" @selected((string) $clubFilter === (string) $c->id)>
                                {{ $c->entity_type === 'company' ? '🏢' : '🏊' }} {{ $c->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">🏢 المركب</label>
                    <select name="complex" class="form-select">
                        <option value="">الكل</option>
                        @foreach($complexes as $cx)
                            <option value="{{ $cx->id }}" @selected((string) $complexFilter === (string) $cx->id)>
                                {{ $cx->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-start gap-2">
                    <button type="submit" class="btn btn-primary">بحث</button>
                    <a href="{{ route('admin.cards') }}" class="btn btn-secondary">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table id="cardsTable" class="table table-bordered table-hover align-middle text-center w-100">
                <thead class="table-dark">
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>#</th>
                    <th>الاسم الكامل</th>
                    <th>النوع</th>
                    <th>الفئة العمرية</th>
                    <th>فصيلة الدم</th>
                    <th>النادي / المؤسسة</th>
                    <th>العنوان</th>
                    <th>النشاط</th>
                    <th>المنشأة</th>
                    <th>الصورة</th>
                    <th>إصدار</th>
                </tr>
                </thead>
                <tbody>
                @foreach($persons as $p)
                    @php
                        $type = $p->user->type ?? 'person';
                        $typeLabels = [
                            'person'    => ['🏃 فرد', 'primary'],
                            'club'      => ['🏊 نادٍ', 'info'],
                            'company'   => ['🏢 مؤسسة', 'secondary'],
                            'entreprise'=> ['🏢 مؤسسة', 'secondary'],
                        ];
                        [$typeLabel, $typeBadge] = $typeLabels[$type] ?? [$type, 'dark'];
                    @endphp
                    <tr>
                        <td><input type="checkbox" class="row-check" value="{{ $p->id }}"></td>
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->firstname }} {{ $p->lastname }}</td>
                        <td><span class="badge bg-{{ $typeBadge }}">{{ $typeLabel }}</span></td>
                        <td>{{ $p->ageCategory->name ?? '—' }}</td>
                        <td>
                            @if($p->blood_type)
                                <span class="badge bg-danger">{{ $p->blood_type }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ optional($p->club)->nom ?? '—' }}</td>
                        <td>
                            <span class="d-inline-block text-truncate" style="max-width:220px" title="{{ $p->address }}">
                                {{ $p->address ?: '—' }}
                            </span>
                        </td>
                        <td>{{ $p->club?->activity?->title
                            ?? $p->lastReservation?->complexActivity?->activity?->title
                            ?? ($p->favorite_activity ?: '—') }}</td>
                        <td>{{ optional($p->complex)->nom ?? optional(optional($p->user)->complex)->nom ?? '—' }}</td>
                        <td>
                            @if($p->photo)
                                <span class="badge bg-success">✔</span>
                            @else
                                <span class="badge bg-danger">✘</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.card', $p->id) }}" target="_blank" class="btn btn-sm btn-success">🪪</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <p class="text-muted small mt-2">✔ = لديه صورة — ✘ = بدون صورة (البطاقة تُطبع مع رمز «لا توجد صورة»)</p>

        </div>
    </div>

</div>
@endsection

@push('js')
@include('admin.partials.datatable-script', ['tableId' => '#cardsTable'])
<script>
$(document).ready(function () {

    $('#selectAll').on('click', function () {
        $('.row-check').prop('checked', this.checked);
    });

    $('#printCards').on('click', function () {
        let selected = [];
        $('.row-check:checked').each(function () {
            selected.push($(this).val());
        });

        if (selected.length === 0) {
            alert("الرجاء اختيار شخص واحد على الأقل");
            return;
        }

        window.open("{{ route('admin.cards.print') }}?ids=" + selected.join(','), "_blank");
    });

});
</script>
@endpush