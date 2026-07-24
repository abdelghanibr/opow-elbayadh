<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>الدفع</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="direction: rtl; text-align:right; font-family:Cairo, sans-serif;">

<div class="container py-5">

    <h3>الدفع للحجز #{{ $reservation->id }}</h3>

    <table class="table">
        <tr><th>المركّب</th><td>{{ $reservation->complex->nom }}</td></tr>
        <tr><th>التاريخ</th><td>{{ $reservation->date_reservation }}</td></tr>
        <tr><th>الوقت</th><td>{{ $reservation->heure_debut }} - {{ $reservation->heure_fin }}</td></tr>
        <tr><th>المبلغ</th><td class="fw-bold">{{ $reservation->montant }} دج</td></tr>
    </table>

    <a href="{{ route('reservation.pay_cash', $reservation->id) }}" class="btn btn-primary w-100 mb-2">
        الدفع نقدًا
    </a>

    <a href="{{ route('reservation.pay_online', $reservation->id) }}" class="btn btn-success w-100">
        الدفع الإلكتروني
    </a>

</div>

</body>
</html>
