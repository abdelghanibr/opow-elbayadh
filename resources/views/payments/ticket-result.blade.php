@extends('layouts.app')

@section('content')

<style>
.opow-card{
    border:none;
    border-radius:14px;
    overflow:hidden;
    background:#fff;
    box-shadow:0 6px 18px rgba(0,0,0,.08);
    margin-bottom:18px;
}
.opow-header{
    background:linear-gradient(135deg,#1b5e20,#2e7d32);
    color:#fff;
    padding:14px 18px;
    font-family:"Cairo",sans-serif;
    font-weight:700;
    font-size:15px;
}
.opow-body{
    padding:18px;
    font-family:"Cairo",sans-serif;
    font-size:14px;
}
.opow-section-title{
    font-weight:700;
    color:#1b5e20;
    margin-bottom:12px;
}
.opow-label{
    color:#2e7d32;
    font-weight:700;
}
.opow-table th{
    background:#f1f8f4;
    color:#1b5e20;
    font-weight:700;
}
.opow-table td{
    background:#fff;
}
.payment-card{
    border:none;
    border-radius:16px;
    box-shadow:0 8px 22px rgba(0,0,0,.08);
}
.price-card{
    border-radius:14px;
    background:linear-gradient(135deg,#198754,#20c997);
    color:#fff;
}
.receipt-title{
    font-weight:700;
    color:#14532d;
}
.btn2026{
    border:none;
    border-radius:10px;
    padding:9px 14px;
    font-size:13px;
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:6px;
    transition:.2s;
}
.btn2026-blue{background:#0d6efd;color:#fff}
.btn2026-blue:hover{background:#0b5ed7}
.btn2026-green{background:#198754;color:#fff}
.btn2026-green:hover{background:#157347}
.btn2026-dark{background:#212529;color:#fff}
.btn2026-dark:hover{background:#111}
.btn2026-red{background:#dc3545;color:#fff}
.btn2026-red:hover{background:#bb2d3b}
.btn-main{
    background:#1b5e20;
    color:#fff;
    border:none;
    border-radius:8px;
}
.btn-main:hover{
    background:#145a1a;
    color:#fff;
}
.popup-email{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:9999;
}
.popup-content{
    background:white;
    padding:25px;
    border-radius:10px;
    width:320px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}
.popup-content input{
    width:100%;
    padding:10px;
    margin-top:10px;
    margin-bottom:15px;
    border:1px solid #ddd;
    border-radius:6px;
}
.popup-actions{
    display:flex;
    justify-content:space-between;
}
</style>

@php
    $satim = is_array($payment->payload ?? null) ? $payment->payload : (json_decode($payment->payload ?? '[]', true) ?: []);
    $dataAttr = data_get($satim, 'data.attributes', []);
    $satimAmount      = data_get($dataAttr, 'amount', data_get($satim, 'Amount'));
    $satimPan         = data_get($dataAttr, 'pan', data_get($satim, 'Pan'));
    $satimCardholder  = data_get($dataAttr, 'cardholderName', data_get($satim, 'cardholderName'));
    $satimAction      = data_get($dataAttr, 'action_code_description', data_get($satim, 'actionCodeDescription', $action ?? null));
    $satimOrderNumber = data_get($dataAttr, 'order_id',
                        data_get($dataAttr, 'order_number',
                        data_get($dataAttr, 'params.OrderNumber',
                        data_get($satim, 'data.id',
                        data_get($satim, 'OrderNumber',
                        $orderNumber ?? ($payment->order_id ?? '—'))))));
    $satimAuthCode    = data_get($dataAttr, 'approval_code',
                        data_get($dataAttr, 'auth_code',
                        data_get($dataAttr, 'params.authorizationResponseId',
                        data_get($satim, 'approval_code',
                        data_get($satim, 'approvalCode',
                        data_get($satim, 'authorizationResponseId'))))));

    if (preg_match('/error\s*code\s*[:=]/i', (string) $satimAction)) {
        $satimAction = null;
    }
@endphp

<div class="container py-4" style="direction: rtl; text-align:right; max-width:900px">

    @if($status === 'paid' || $status === 'success')
        <div class="alert alert-success border shadow-sm">
            <div class="fw-bold mb-1">✔ تم الدفع بنجاح</div>
            <small>
                {{ $satimAction ?? $action ?? 'نشكركم على إتمام عملية الدفع بنجاح. يمكنكم الاحتفاظ بهذا الوصل كمرجع رسمي.' }}
            </small>
        </div>
    @elseif($status === 'failed')
        <div class="alert alert-danger border shadow-sm">
            <div class="fw-bold mb-1">❌ فشل في عملية الدفع</div>
            <small class="text-muted">
                {{ $action ?? 'لم تكتمل عملية الدفع. يرجى المحاولة مرة أخرى أو التواصل مع الدعم.' }}
          
        
            </small>
        </div>
    @else
        <div class="alert alert-warning border shadow-sm">
            <div class="fw-bold mb-1">⏳ الدفع قيد المعالجة</div>
            <small>
                {{ $action ?? 'عملية الدفع لم تُؤكد بعد. يرجى الانتظار أو تحديث الصفحة لاحقًا.' }}
          
            </small>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

    @if($status === 'paid' || $status === 'success')

        <div class="row g-4">

            <div class="col-md-5">

                <div class="opow-card">
                    <div class="opow-header">
                        🏟️ ديوان المركب المتعدد الرياضات لولاية البيض
                    </div>

                    <div class="opow-body">
                        <div class="opow-section-title">👤 معلومات الحاجز</div>

                        <p><span class="opow-label">الاسم:</span> {{ $ticket->buyer_name ?? '—' }}</p>
                        <p><span class="opow-label">الهاتف:</span> {{ $ticket->buyer_phone ?? '—' }}</p>
                        @if(isset($ticketData['email']) && $ticketData['email'])
                            <p><span class="opow-label">البريد الإلكتروني:</span> {{ $ticketData['email'] }}</p>
                        @endif
                    </div>
                </div>

                <div class="opow-card">
                    <div class="opow-header">
                        ⚽ تفاصيل المباراة
                    </div>

                    <div class="opow-body p-0">
                        <table class="table opow-table text-center align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>الفريقين</th>
                                    <th>نوع المقعد</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $ticket->match->homeTeam->name ?? '—' }} vs {{ $ticket->match->awayTeam->name ?? '—' }}</td>
                                    <td>{{ $ticket->seatType->name ?? '—' }}</td>
                                    <td>{{ $ticket->match->match_date ?? '—' }} {{ $ticket->match->match_time ?? '' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($ticket->qr_code)
                <div class="opow-card">
                    <div class="opow-header">
                        📱 رمز التذكرة
                    </div>
                    <div class="opow-body text-center">
                        <div style="display:inline-block; padding:6px; border:3px solid #1b5e20; border-radius:12px;">
                            {!! QrCode::size(130)->encoding('UTF-8')->generate($ticket->qr_code) !!}
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <div class="col-md-7">
                <div class="card shadow-sm p-4">

                    <h6 class="fw-bold mb-3">💳 معلومات الدفع</h6>

                    <p><strong>رقم التذكرة:</strong> #{{ $ticket->id ?? '—' }}</p>
                    <p><strong>رقم الوصل المحلي:</strong> {{ $payment->order_id ?? '—' }}</p>
                    <p><strong>رقم العملية:</strong> {{ $satimOrderNumber ?? '—' }}</p>
                    <p><strong>رمز الموافقة:</strong> {{ $satimAuthCode ?? '—' }}</p>

                    <p>
                        <strong>تاريخ الدفع:</strong>
                        {{ $payment->datetimesatim ? $payment->datetimesatim->format('Y-m-d H:i') : '-' }}
                    </p>

                   <p><strong>طريقة الدفع:</strong> البطاقة الذهبية/ CIB </p>

                    @if($satimCardholder)
                        <p><strong>اسم حامل البطاقة:</strong> {{ $satimCardholder }}</p>
                    @endif

                    @if($satimPan)
                        <p><strong>رقم البطاقة:</strong> {{ $satimPan }}</p>
                    @endif

                    <div class="card border-0 shadow-sm mb-3"
                         style="background: linear-gradient(135deg,#198754,#20c997); color:#fff">
                        <div class="card-body text-center py-4">
                            <div class="mb-2" style="font-size:14px; opacity:.9">
                                💰 المبلغ المدفوع
                            </div>
                            <div style="font-size:32px; font-weight:800">
                                {{ number_format($payment->amount ?? 0, 0) }} دج
                            </div>
                            <small style="opacity:.85">
                                @if($satimAmount)
                                    {{ number_format($satimAmount, 0) }} دج
                                @else
                                    شامل كل الرسوم
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="receipt-actions mt-4">
                        <div class="receipt-title mb-2">
                            🧾 إدارة الوصل
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('payment.receipt', $payment->order_id) }}"
                               target="_blank"
                               class="btn2026 btn2026-blue">
                                <i class="fa-solid fa-file-arrow-down"></i>
                            تحميل وصل الدفع 
                            </a>

                            <button type="button"
                                    class="btn2026 btn2026-green"
                                    onclick="openEmailPopup()">
                                <i class="fa-solid fa-envelope"></i>
                                إرسال بالبريد
                            </button>

                            <button type="button"
                                    class="btn2026 btn2026-dark"
                                    onclick="printTicket({{ $ticket->id }})">
                                <i class="fa-solid fa-print"></i>
                                طباعة التذكرة
                            </button>
                        </div>
                    </div>

           <div class="text-center mt-4">

    <a href="{{ route('matches.public') }}"
       class="btn btn-main btn-sm px-4 mb-3">
        ⬅️ عرض المباريات
    </a>



</div>

                </div> 
            </div>

        </div>

        <div class="text-center text-muted small mt-4">
            هذا الوصل تم إنشاؤه إلكترونيًا — {{ now()->format('Y-m-d H:i') }}
        </div>

    @else
        <div class="text-center mt-4">
            <a href="{{ route('matches.public') }}"
               class="btn btn-main btn-sm px-4">
                ⬅️ عرض المباريات
            </a>
        </div>
    @endif
       <div class="text-center mt-3">

    <div style="font-size:13.5px; color:#14532d; margin-bottom:6px;">
        في حال وجود مشكلة في بطاقتك CIB أو الذهبية<br>
        يرجى الاتصال بمركز الدعم SATIM
    </div>

    <img src="{{ asset('images/app.png') }}"
         alt="SATIM 3020"
         style="height:48px">

</div>
</div>

@if(isset($payment) && $payment && ($status === 'paid' || $status === 'success'))
<div id="emailPopup" class="popup-email">
    <div class="popup-content">
        <h3>إرسال الإيصال بالبريد</h3>

        <form method="POST"
              action="{{ route('ticket.receipt.email', $ticket->id) }}"
              id="emailReceiptForm">
            @csrf

            <input type="email"
                   name="email"
                   value="{{ $ticketData['email'] ?? '' }}"
                   placeholder="example@email.com"
                   required>

            <div class="popup-actions">
                <button type="submit" class="btn2026 btn2026-green" id="sendReceiptBtn">
                    إرسال
                </button>

                <button type="button" onclick="closeEmailPopup()" class="btn2026 btn2026-red">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function openEmailPopup() {
    const popup = document.getElementById("emailPopup");
    if (popup) popup.style.display = "flex";
}

function closeEmailPopup() {
    const popup = document.getElementById("emailPopup");
    if (popup) popup.style.display = "none";
}

function printTicket(id) {
    window.open("{{ url('/tickets/print') }}/" + id, "_blank");
}

(function () {
    if (window.history.replaceState) {
        window.history.replaceState(null, "", window.location.href);
    }

    window.history.pushState(null, "", window.location.href);

    window.addEventListener("popstate", function () {
        window.location.replace("{{ route('matches.public') }}");
    });
})();

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("emailReceiptForm");
    if (!form) return;

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById("sendReceiptBtn");
        const emailInput = form.querySelector('input[name="email"]');

        try {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = "جاري الإرسال...";
            }

            const formData = new FormData();
            formData.append("_token", "{{ csrf_token() }}");
            formData.append("email", emailInput.value);

            const response = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message || "تم إرسال الوصل بنجاح");
                closeEmailPopup();
            } else {
                alert(result.message || "فشل إرسال البريد");
            }

        } catch (error) {
            console.error("Erreur envoi email :", error);
            alert("تعذر إرسال البريد");
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = "إرسال";
            }
        }
    });
});
</script>
@endsection