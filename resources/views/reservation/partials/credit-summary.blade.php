{{-- Bandeau « solde de remboursement » : synthèse + détail repliable (données = ReservationCreditService::summary) --}}
@php
    $fmt  = fn ($v) => number_format((float) $v, 2, '.', '');
    $days = [0 => 'الأحد', 1 => 'الإثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت'];
    $states = [
        'available' => ['متاح', 'bg-success'],
        'reserved'  => ['محجوز', 'bg-warning text-dark'],
        'used'      => ['مستعمل', 'bg-secondary'],
    ];
@endphp

@if(!empty($creditSummary) && $creditSummary['total'] > 0)
    <div class="alert alert-info mb-4" dir="rtl">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <div class="fw-semibold">
                إجمالي التعويضات : <span dir="ltr">{{ $fmt($creditSummary['total']) }}</span> دج
                &nbsp;|&nbsp; المستعمل : <span dir="ltr">{{ $fmt($creditSummary['used']) }}</span> دج
                &nbsp;|&nbsp; محجوز لحجوزات قيد الانتظار : <span dir="ltr">{{ $fmt($creditSummary['reserved']) }}</span> دج
                &nbsp;|&nbsp; المتاح : <span dir="ltr">{{ $fmt($creditSummary['available']) }}</span> دج
            </div>

            @if($creditSummary['available'] > 0)
                <span class="badge bg-primary">سيتم خصمه من الحجز القادم</span>
            @endif
        </div>

        <details class="mt-2">
            <summary class="fw-semibold" style="cursor:pointer">تفاصيل التعويضات</summary>
            <div class="table-responsive mt-2">
                <table class="table table-sm table-bordered align-middle text-center mb-0 bg-white">
                    <thead class="table-light">
                        <tr>
                            <th>تاريخ الإغلاق</th>
                            <th>الحصة الملغاة</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>الحجز المرتبط</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($creditSummary['rows'] as $row)
                        @php
                            $date = \Carbon\Carbon::parse($row['closure_date']);
                            [$label, $cls] = $states[$row['state']];
                        @endphp
                        <tr>
                            <td><span dir="ltr">{{ $date->format('Y-m-d') }}</span></td>
                            <td>{{ $days[$date->dayOfWeek] }}</td>
                            <td><span dir="ltr">{{ $fmt($row['amount']) }}</span> دج</td>
                            <td><span class="badge {{ $cls }}">{{ $label }}</span></td>
                            <td>
                                @if($row['reservation'])
                                    #{{ $row['reservation']->id }} {{ $row['reservation']->season?->name }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </details>
    </div>
@endif
