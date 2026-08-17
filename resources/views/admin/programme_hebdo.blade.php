@extends('layouts.app')

@section('content')
<style>
    body {
        font-family: "Cairo", sans-serif !important;
        background: #f4f7fb;
    }

    .prog-header {
        background:
            radial-gradient(circle at top left, rgba(20,184,166,.35), transparent 35%),
            linear-gradient(135deg, #082f49, #075985);
        color: #fff;
        border-radius: 20px;
        padding: 20px 24px;
        box-shadow: 0 14px 30px rgba(8,47,73,.18);
    }
</style>

<div class="container py-4" style="direction: rtl; text-align:right;">

    <div class="prog-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">📅 البرنامج الأسبوعي للمنشأة</h4>
            <span class="text-white-50">{{ $complex->nom }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark fs-6">
                {{ $weekDays[0]->format('d/m/Y') }} ← {{ $weekDays[6]->format('d/m/Y') }}
            </span>
            <a href="{{ route('admin.complex.programme', [$complex->id, 'start' => $prevWeek]) }}"
               class="btn btn-outline-light btn-sm">⬅ الأسبوع السابق</a>
            <a href="{{ route('admin.complex.programme', [$complex->id, 'start' => $nextWeek]) }}"
               class="btn btn-outline-light btn-sm">الأسبوع التالي ➡</a>
            <a href="{{ route('admin.dashboard_complex', $complex->id) }}" class="btn btn-light fw-bold">
                ⬅ الرجوع للوحة
            </a>
        </div>
    </div>

    @php
        $weekLabels = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
    @endphp

    <div class="card shadow-sm" style="border-radius: 20px; border: 1px solid #e5eaf2;">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(135deg, #0a4f88, #0a8a67); border-radius: 20px 20px 0 0;">
            أفواج النشاط في أسبوع واحد محدد
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-top mb-0" style="min-width: 1000px;">
                    <thead class="table-dark">
                        <tr>
                            @foreach($weekDays as $i => $d)
                                <th>
                                    {{ $weekLabels[$i] }}<br>
                                    <small class="text-white-50">{{ $d->format('d/m') }}</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach($calendarDays as $i => $items)
                                <td style="vertical-align: top; background: #f8fafc;">
                                    @forelse($items as $item)
                                        <div class="mb-2 text-start p-2 rounded"
                                             style="border-right: 5px solid {{ $item->color }}; background: {{ $item->color }}14;">
                                            <div class="fw-bold" style="color: {{ $item->color }};">
                                                {{ $item->schedule->groupe }}
                                            </div>
                                            <small class="d-block text-muted">{{ $item->activity_title }}</small>
                                            <small class="d-block">
                                                ⏱ {{ $item->start }} → {{ $item->end }}
                                            </small>
                                            <span class="badge mt-1"
                                                  style="background: {{ $item->color }}; color: #fff;">
                                                📝 {{ $item->reservations }} حجز
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-muted small">—</span>
                                    @endforelse
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($activeSchedules->isNotEmpty())
                <hr>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $seen = [];
                    @endphp
                    @foreach($activeSchedules as $s)
                        @php $a = $s->complexActivity->activity; @endphp
                        @if($a && !in_array($a->id, $seen))
                            @php $seen[] = $a->id; @endphp
                            <span class="badge" style="background: {{ $a->color ?? '#0ea5e9' }}; color: #fff;">
                                {{ $a->title }}
                            </span>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
