{{-- Détail du prix d'une réservation (valeurs figées à la création, jamais recalculées ici).
     Une seule ligne par information, 2 décimales. --}}
@php
    $fmt      = fn ($v) => number_format((float) $v, 2, '.', '');
    $d        = is_array($r->price_detail) ? $r->price_detail : [];
    $discount = (float) $r->credit_discount;
    $hasSessions = isset($d['sessions_total'], $d['sessions_counted'], $d['unit_price']);
    $before   = $d['credit_balance_before'] ?? null;
    $after    = $d['credit_balance_after'] ?? null;
    $hasCredit = $discount > 0 || (float) ($before ?? 0) > 0;
@endphp

<div class="text-start" style="min-width:170px">
    {{-- 01 — valeur des séances du mois --}}
    @if($hasSessions)
        <div class="small text-muted">عدد حصص الشهر : <span dir="ltr">{{ (int) $d['sessions_total'] }}</span></div>
        <div class="small text-muted">الحصص المحسوبة : <span dir="ltr">{{ (int) $d['sessions_counted'] }}</span></div>
        <div class="small text-muted">سعر الحصة : <span dir="ltr">{{ $fmt($d['unit_price']) }}</span> دج</div>
        <div class="small text-muted">قيمة الحصص : <span dir="ltr">{{ $fmt($d['sessions_value'] ?? $r->initial_price) }}</span> دج</div>
    @elseif($discount > 0)
        {{-- anciennes réservations sans détail : seulement ce qui est connu --}}
        <div class="small text-muted">سعر الشهر : <span dir="ltr">{{ $fmt($r->initial_price) }}</span> دج</div>
    @endif

    {{-- 02 — solde de remboursement (seulement s'il existe un crédit ou une remise) --}}
    @if($hasCredit)
        @if($before !== null)
            <div class="small text-muted">الرصيد التعويضي الإجمالي : <span dir="ltr">{{ $fmt($before) }}</span> دج</div>
        @endif
        <div class="small text-muted">الخصم من هذا الحجز : <span dir="ltr">{{ $fmt($discount) }}</span> دج</div>
        @if($after !== null)
            <div class="small text-muted">الرصيد المتبقي : <span dir="ltr">{{ $fmt($after) }}</span> دج</div>
        @endif
        <div class="fw-bold">المتبقي للدفع : <span dir="ltr">{{ $fmt($r->total_price) }}</span> دج</div>
        @if((float) $r->credit_waived > 0)
            <div class="small text-muted">مُسقَط : <span dir="ltr">{{ $fmt($r->credit_waived) }}</span> دج</div>
        @endif
    @else
        <div class="fw-bold"><span dir="ltr">{{ $fmt($r->total_price ?? 0) }}</span> دج</div>
    @endif
</div>
