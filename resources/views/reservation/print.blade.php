<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة الحجز</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* ===== Reset ===== */
html, body {
    width: 210mm;
    height: 297mm;
    margin: 0 !important;
    padding: 0 !important;
}

* {
    box-sizing: border-box;
}

/* ===== Page Setup ===== */
@page {
    size: A4;
    margin: 0;
}

/* ===== Body ===== */
body {
    font-family: "IBM Plex Sans Arabic", "Cairo", sans-serif;
    direction: rtl;
    background: #f4f6f8;
}

/* ===== A4 Container ===== */
.a4 {
    width: 210mm;
    height: 297mm;
    padding: 15mm;
    background: #fff;
    overflow: hidden;
}

/* ===== Header ===== */
 .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header img {
            height: 70px;
            margin-bottom: 8px;
        }

.header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
}

.header h4 {
    margin: 0;
    font-size: 14px;
    font-weight: 500;
    color: #444;
    text-align: center;
}

/* ===== Titles ===== */
h2 {
    text-align: center;
    margin: 6px 0;
    font-size: 22px;
    font-weight: 800;
}

h3 {
    text-align: center;
    margin: 4px 0 12px;
    font-size: 16px;
    font-weight: 600;
}

/* ===== Sections ===== */
.section {
    margin-bottom: 10px;
}

/* ===== Box ===== */
.box {
    border: 1px solid #000;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.8;
}

/* ===== Tables ===== */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

table th {
    background: #f0f0f0;
    font-weight: 700;
}

table th,
table td {
    border: 1px solid #000;
    padding: 6px;
    text-align: center;
}

/* ===== QR ===== */
.qr {
    margin-top: 12px;
    text-align: center;
}

.qr img {
    width: 110px;
}

/* ===== Footer ===== */
.footer {
    margin-top: 14px;
    display: flex;
    justify-content: space-between;
    font-size: 13px;
}

/* ===== Actions ===== */
.actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 22px;
}

.btn {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.btn-print {
    background: #000;
    color: #fff;
}

.btn-download {
    background: #0d6efd;
    color: #fff;
}

.btn-mail {
    background: #198754;
    color: #fff;
}

.btn:hover {
    opacity: 0.85;
}

/* ===== Print ===== */
@media print {
    body {
        background: #fff;
    }

    .no-print {
        display: none !important;
    }
}
</style>
</head>

<body>

@php
    $user = $reservation->user;

    $beneficiaryName =
        ($user?->type === 'person')
            ? $user->name
            : ($user->name ?? '—');

    $phone =
        $user?->person->phone
        ?? $user->phone
        ?? '';

    $qrData = [
        'reservation_id' => $reservation->id,
        'beneficiary' => $beneficiaryName,
        'type' => $user?->type,
        'phone' => $phone,
        'activity' => $reservation->complexActivity?->activity?->title,
        'start_date' => $reservation->start_date,
        'end_date' => $reservation->end_date,
    ];
@endphp

<div class="a4">

    {{-- Header --}}
{{-- ================= HEADER ================= --}}
<div class="header">
    <img src="{{ asset('images/djs-logo.png') }}">
    <h3>ديوان المركب المتعدد الرياضات</h3>
     <h4>ولاية البيض</h4>

</div>

    {{-- Title --}}
 <h2>وصل حجز</h2>

<h3>
    {{ $reservation->complexActivity?->activity?->title }}
</h3>

<div style="text-align:center; font-size:14px; margin-bottom:12px;">
    <strong>المركب:</strong>
    {{ $reservation->complexActivity?->complex?->nom ?? '—' }}
    &nbsp; | &nbsp;
    <strong>رقم الطلب:</strong>
    {{ $reservation->payment?->order_id ?? '—' }}
</div>


    {{-- Beneficiary --}}
    <div class="section box">
        <strong>المستفيد:</strong> {{ $beneficiaryName }} <br>
        <strong>البريد الإلكتروني:</strong> {{ $user->email ?? '—' }} <br>
        <strong>الهاتف:</strong> {{ $phone }}
    </div>

    {{-- Reservation Info --}}
    <div class="section box">
        <strong>تاريخ البداية:</strong> {{ $reservation->start_date }} <br>
        <strong>تاريخ النهاية:</strong> {{ $reservation->end_date }} <br>
        <strong>السعر:</strong> {{ number_format($reservation->total_price ?? 0) }} دج
    </div>

    {{-- Time Slots --}}
    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>اليوم</th>
                    <th>من</th>
                    <th>إلى</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservation->time_slots ?? [] as $slot)
                    <tr>
                        <td>{{ $reservation->getDayName($slot['day_number']) }}</td>
                        <td>{{ $slot['start'] }}</td>
                        <td>{{ $slot['end'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- QR --}}
    <div class="qr">
        <strong>QR معلومات الحجز</strong><br>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode(json_encode($qrData)) }}">
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div>توقيع المستفيد</div>
        <div>توقيع الإدارة</div>
    </div>

    {{-- Actions --}}
    <div class="actions no-print">
        <button class="btn btn-print" onclick="window.print()">🖨️ طباعة</button>
        <button class="btn btn-download" onclick="downloadPDF()">⬇️ تحميل PDF</button>
     
    </div>

</div>

<script>
function downloadPDF() {
    window.print();
}

function sendByEmail() {
    alert('📧 اربط هذا الزر بـ Route Laravel لإرسال الوصل بالبريد');
}
</script>

</body>
</html>
