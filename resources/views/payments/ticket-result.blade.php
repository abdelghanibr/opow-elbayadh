@extends('layouts.app')

@section('title', 'نتيجة الدفع')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right; max-width:700px">

    <div class="ticket-result-card @if($status === 'success') success @elseif($status === 'failed') failed @else pending @endif">

        <div class="ticket-result-icon">
            @if($status === 'success')
                <i class="fa-solid fa-circle-check"></i>
            @elseif($status === 'failed')
                <i class="fa-solid fa-circle-xmark"></i>
            @else
                <i class="fa-solid fa-clock"></i>
            @endif
        </div>

        <h4 class="fw-bold">
            @if($status === 'success')
                تم الدفع بنجاح
            @elseif($status === 'failed')
                فشل الدفع
            @else
                قيد المعالجة
            @endif
        </h4>

        <p class="ticket-result-action">{{ $action }}</p>

        @if($ticket)
            <div class="ticket-result-details">
                <div class="ticket-result-row">
                    <span>رقم التذكرة</span>
                    <strong>#{{ $ticket->id }}</strong>
                </div>
                <div class="ticket-result-row">
                    <span>المباراة</span>
                    <strong>{{ $ticket->match->homeTeam->name }} vs {{ $ticket->match->awayTeam->name }}</strong>
                </div>
                <div class="ticket-result-row">
                    <span>نوع المقعد</span>
                    <strong>{{ $ticket->seatType->name }}</strong>
                </div>
                <div class="ticket-result-row">
                    <span>رقم الطلب</span>
                    <strong>{{ $payment->order_id }}</strong>
                </div>
                @if($ticket->qr_code)
                    <div class="ticket-result-qr text-center mt-3">
                        <img src="data:image/png;base64,{{ $ticket->qr_code }}" alt="QR Code" style="max-width:180px; border-radius:12px; border:3px solid #e0e6f1;">
                    </div>
                @endif
            </div>
        @endif

        <div class="ticket-result-actions mt-4">
            <a href="{{ route('matches.public') }}" class="ticket-result-btn primary">
                <i class="fa-solid fa-futbol"></i>
                العودة للمباريات
            </a>
            @if($status === 'success' && $payment)
                <a href="{{ route('ticket.payment.verify') }}?order_number={{ $payment->order_id }}" class="ticket-result-btn outline">
                    <i class="fa-solid fa-receipt"></i>
                    تحميل الوصل
                </a>
            @endif
        </div>

    </div>

</div>
@endsection

@push('css')
<style>
body { font-family: "Cairo", sans-serif !important; }

.ticket-result-card {
    background: #fff;
    border: 2px solid rgba(8,47,87,0.10);
    border-radius: 24px;
    padding: 40px 30px;
    text-align: center;
    box-shadow: 0 12px 36px rgba(8,47,87,0.10);
}

.ticket-result-card.success { border-top: 5px solid #12a86b; }
.ticket-result-card.failed { border-top: 5px solid #dc2626; }
.ticket-result-card.pending { border-top: 5px solid #eab308; }

.ticket-result-icon {
    font-size: 4rem;
    margin-bottom: 16px;
}

.ticket-result-card.success .ticket-result-icon { color: #12a86b; }
.ticket-result-card.failed .ticket-result-icon { color: #dc2626; }
.ticket-result-card.pending .ticket-result-icon { color: #eab308; }

.ticket-result-action {
    color: #64748b;
    font-size: 0.95rem;
    margin-bottom: 24px;
}

.ticket-result-details {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    text-align: right;
}

.ticket-result-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e2e8f0;
}

.ticket-result-row:last-child { border-bottom: none; }

.ticket-result-row span { color: #64748b; font-weight: 700; }
.ticket-result-row strong { color: #082f57; font-weight: 800; }

.ticket-result-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.ticket-result-btn {
    padding: 12px 24px;
    border-radius: 14px;
    font-weight: 800;
    font-size: 0.95rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.ticket-result-btn.primary {
    background: linear-gradient(135deg, #082f57, #0b3d70);
    color: #fff;
}

.ticket-result-btn.primary:hover {
    transform: translateY(-2px);
    color: #fff;
}

.ticket-result-btn.outline {
    background: #fff;
    color: #082f57;
    border: 2px solid #082f57;
}

.ticket-result-btn.outline:hover {
    background: #082f57;
    color: #fff;
}
</style>
@endpush
