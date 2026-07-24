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
    $satim = isset($satim_data) && is_array($satim_data)
        ? $satim_data
        : (is_array($payment->payload ?? null) ? $payment->payload : (json_decode($payment->payload ?? '[]', true) ?: []));

    $satimAmount      = data_get($satim, 'Amount');
    $satimCurrency    = data_get($satim, 'currency', '012');
    $satimPan         = data_get($satim, 'Pan');
    $satimCardholder  = data_get($satim, 'cardholderName');
    $satimAction      = data_get($satim, 'actionCodeDescription', $action ?? null);
    $satimOrderNumber = data_get($satim, 'OrderNumber', $order_id ?? ($payment->order_id ?? '—'));
    $satimErrorCode   = data_get($satim, 'ErrorCode');
    $satimAuthId      = data_get($satim, 'authorizationResponseId');
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
                        <div class="opow-section-title">👤 معلومات المستفيد</div>

                        <p><span class="opow-label">الاسم:</span> {{ optional($user)->name ?? '—' }}</p>
                        <p><span class="opow-label">البريد الإلكتروني:</span> {{ optional($user)->email ?? '—' }}</p>

                        @if(optional($user)->phone)
                            <p><span class="opow-label">الهاتف:</span> {{ $user->phone }}</p>
                        @endif
                    </div>
                </div>

                <div class="opow-card">
                    <div class="opow-header">
                        📅 تفاصيل الحجز
                    </div>

                    <div class="opow-body p-0">
                        <table class="table opow-table text-center align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>النشاط</th>
                                    <th>المنشأة</th>
                                    <th>مدة الحجز</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $reservation->complexActivity->activity->title ?? '—' }}</td>
                                    <td>{{ $reservation->complexActivity->complex->nom ?? '—' }}</td>
                                    <td>{{ $reservation->season->name ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="col-md-7">
                <div class="card shadow-sm p-4">

                    <h6 class="fw-bold mb-3">💳 معلومات الدفع</h6>

                    <p><strong>رقم الوصل المحلي:</strong> {{ $payment->order_id ?? '—' }}</p>
                    <p><strong>رقم العملية SATIM:</strong> {{ $satimOrderNumber ?? '—' }}</p>
                    <p><strong>رمز الموافقة:</strong> {{ $approval_code ?? '—' }}</p>
                    <p><strong>معرف التفويض:</strong> {{ $satimAuthId ?? '—' }}</p>

                    <p>
                        <strong>تاريخ الدفع:</strong>
                        {{ $payment->datetimesatim ? $payment->datetimesatim->format('Y-m-d H:i') : '-' }}
                    </p>

                    <p><strong>طريقة الدفع:</strong> بطاقة بنكية (SATIM)</p>

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
                                {{ number_format(($payment->amount ?? 0) / 100, 2) }} دج
                            </div>
                            <small style="opacity:.85">
                                @if($satimAmount)
                                    SATIM: {{ number_format($satimAmount / 100, 2) }} دج
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
                         <button type="button"
        class="btn2026 btn2026-blue"
        onclick="downloadReceiptPdf()">
    <i class="fa-solid fa-file-arrow-down"></i>
    تحميل وصل الدفع
</button>

                            <button type="button"
                                    class="btn2026 btn2026-green"
                                    onclick="openEmailPopup()">
                                <i class="fa-solid fa-envelope"></i>
                                إرسال بالبريد
                            </button>

                            @if($reservation)
                                <button type="button"
                                        class="btn2026 btn2026-dark"
                                        onclick="printReservation({{ $reservation->id }})">
                                    <i class="fa-solid fa-print"></i>
                                    طباعة وصل الحجز
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('reservation.my-reservations') }}"
                           class="btn btn-main btn-sm px-4">
                            ⬅️ عرض الحجوزات
                        </a>
                    </div>

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
            </div>

        </div>

        <div class="text-center text-muted small mt-4">
            هذا الوصل تم إنشاؤه إلكترونيًا — {{ now()->format('Y-m-d H:i') }}
        </div>

    @else
        <div class="text-center mt-4">
            <a href="{{ route('reservation.my-reservations') }}"
               class="btn btn-main btn-sm px-4">
                ⬅️ عرض الحجوزات
            </a>

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
    @endif

</div>

@if(isset($payment) && $payment && ($status === 'paid' || $status === 'success'))
<div id="emailPopup" class="popup-email">
    <div class="popup-content">
        <h3>إرسال الإيصال بالبريد</h3>

        <form method="POST"
              action="{{ route('payment.receipt.email', $payment->order_id) }}"
              id="emailReceiptForm">
            @csrf

            <input type="email"
                   name="email"
                   value="{{ optional($user)->email }}"
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
@php
    $satim = isset($satim_data) && is_array($satim_data)
        ? $satim_data
        : (is_array($payment->payload ?? null) ? $payment->payload : (json_decode($payment->payload ?? '[]', true) ?: []));
@endphp
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<div id="receiptPdfContent" style="
    position:absolute;
    left:-99999px;
    top:0;
    width:800px;
    background:#ffffff;
    color:#000;
    padding:30px;
    font-family:'Cairo', sans-serif;
    direction: rtl;
    text-align: right;
    line-height:1.8;
">

    <div style="border:1px solid #ddd; border-radius:12px; padding:25px; font-family:'Cairo', sans-serif;">
        <h2 style="margin:0 0 10px; color:#14532d; font-family:'Cairo', sans-serif; font-weight:800; font-size:34px;">
            وصل دفع إلكتروني
        </h2>

        <h3 style="margin:0 0 20px; color:#1b5e20; font-family:'Cairo', sans-serif; font-weight:700; font-size:24px;">
            ديوان المركب المتعدد الرياضات لولاية البيض
        </h3>

        <table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-family:'Cairo', sans-serif;">
            <tr>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:700;">
                    رقم الوصل المحلي
                </td>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                    {{ $payment->order_id ?? '—' }}
                </td>
            </tr>
            <tr>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:700;">
                    رقم العملية SATIM
                </td>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                    {{ data_get($satim, 'OrderNumber', $order_id ?? '—') }}
                </td>
            </tr>
            <tr>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:700;">
                    رمز الموافقة
                </td>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                    {{ $approval_code ?? '—' }}
                </td>
            </tr>
            <tr>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:700;">
                    حالة العملية
                </td>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                    {{ data_get($satim, 'actionCodeDescription', $action ?? '—') }}
                </td>
            </tr>
            <tr>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:700;">
                    تاريخ الدفع
                </td>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                    {{ $payment->datetimesatim ? $payment->datetimesatim->format('Y-m-d H:i') : now()->format('Y-m-d H:i') }}
                </td>
            </tr>
            <tr>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:700;">
                    المبلغ
                </td>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:600;">
                    {{ number_format(($payment->amount ?? 0) / 100, 2) }} دج
                </td>
            </tr>
        </table>

        <h4 style="color:#14532d; font-family:'Cairo', sans-serif; font-weight:700; font-size:22px; margin-bottom:12px;">
            معلومات المستفيد
        </h4>

        <table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-family:'Cairo', sans-serif;">
            <tr>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:700;">
                    الاسم
                </td>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                    {{ optional($user)->name ?? '—' }}
                </td>
            </tr>
            <tr>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:700;">
                    البريد الإلكتروني
                </td>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                    {{ optional($user)->email ?? '—' }}
                </td>
            </tr>
            @if(optional($user)->phone)
            <tr>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:700;">
                    الهاتف
                </td>
                <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                    {{ $user->phone }}
                </td>
            </tr>
            @endif
        </table>

        @if($reservation)
        <h4 style="color:#14532d; font-family:'Cairo', sans-serif; font-weight:700; font-size:22px; margin-bottom:12px;">
            تفاصيل الحجز
        </h4>

        <table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-family:'Cairo', sans-serif;">
            <thead>
                <tr>
                    <th style="padding:10px; border:1px solid #ddd; background:#f1f8f4; font-family:'Cairo', sans-serif; font-weight:700;">
                        النشاط
                    </th>
                    <th style="padding:10px; border:1px solid #ddd; background:#f1f8f4; font-family:'Cairo', sans-serif; font-weight:700;">
                        المنشأة
                    </th>
                    <th style="padding:10px; border:1px solid #ddd; background:#f1f8f4; font-family:'Cairo', sans-serif; font-weight:700;">
                        المدة
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                        {{ $reservation->complexActivity->activity->title ?? '—' }}
                    </td>
                    <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                        {{ $reservation->complexActivity->complex->nom ?? '—' }}
                    </td>
                    <td style="padding:10px; border:1px solid #ddd; font-family:'Cairo', sans-serif; font-weight:500;">
                        {{ $reservation->season->name ?? '—' }}
                    </td>
                </tr>
            </tbody>
        </table>
        @endif

        <div style="margin-top:20px; font-size:13px; color:#666; font-family:'Cairo', sans-serif; font-weight:500;">
            تم إنشاء هذا الوصل إلكترونيًا بتاريخ {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
function openEmailPopup() {
    const popup = document.getElementById("emailPopup");
    if (popup) popup.style.display = "flex";
}

function closeEmailPopup() {
    const popup = document.getElementById("emailPopup");
    if (popup) popup.style.display = "none";
}

function printReservation(id) {
    window.open("{{ url('/reservations') }}/" + id + "/print", "_blank");
}

(function () {
    if (window.history.replaceState) {
        window.history.replaceState(null, "", window.location.href);
    }

    window.history.pushState(null, "", window.location.href);

    window.addEventListener("popstate", function () {
        window.location.replace("{{ route('reservation.my-reservations') }}");
    });
})();

async function generateReceiptPdf(mode = "download") {
    const element = document.getElementById('receiptPdfContent');

    if (!element) {
        throw new Error("Element #receiptPdfContent introuvable");
    }

    if (!window.jspdf || !window.html2canvas) {
        throw new Error("jsPDF ou html2canvas non chargé");
    }

    const { jsPDF } = window.jspdf;

    const canvas = await html2canvas(element, {
        scale: 1.5,
        useCORS: true,
        backgroundColor: '#ffffff'
    });

    const imgData = canvas.toDataURL('image/jpeg', 0.85);

    const pdf = new jsPDF('p', 'mm', 'a4');
    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();

    const margin = 5;
    const usableWidth = pageWidth - (margin * 2);
    const usableHeight = pageHeight - (margin * 2);

    const imgWidth = usableWidth;
    const imgHeight = (canvas.height * imgWidth) / canvas.width;

    let heightLeft = imgHeight;
    let position = margin;

    pdf.addImage(imgData, 'JPEG', margin, position, imgWidth, imgHeight);
    heightLeft -= usableHeight;

    while (heightLeft > 0) {
        position = heightLeft - imgHeight + margin;
        pdf.addPage();
        pdf.addImage(imgData, 'JPEG', margin, position, imgWidth, imgHeight);
        heightLeft -= usableHeight;
    }

    if (mode === "download") {
        pdf.save('recu-paiement-{{ $payment->order_id ?? "receipt" }}.pdf');
        return true;
    }

    if (mode === "blob") {
        return pdf.output("blob");
    }

    throw new Error("Mode non supporté");
}

async function downloadReceiptPdf() {
    try {
        await generateReceiptPdf("download");
    } catch (error) {
        console.error('Erreur génération PDF :', error);
        alert('تعذر إنشاء ملف PDF');
    }
}

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
                submitBtn.innerHTML = "جاري التحضير...";
            }

            const pdfBlob = await generateReceiptPdf("blob");

            const formData = new FormData();
            formData.append("_token", "{{ csrf_token() }}");
            formData.append("email", emailInput.value);
            formData.append("pdf_file", pdfBlob, "recu-paiement-{{ $payment->order_id ?? 'receipt' }}.pdf");

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
                window.location.reload();
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