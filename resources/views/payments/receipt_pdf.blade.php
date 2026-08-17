<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>وصل دفع</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #222;
            direction: rtl;
            text-align: right;
        }

        .box {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
        }

        h2, h3 {
            margin: 0 0 10px;
        }

        p {
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        td, th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }

        .mt {
            margin-top: 15px;
        }
    </style>
</head>
<body>
@php
    $satim = isset($satim_data) && is_array($satim_data)
        ? $satim_data
        : (is_array($payment->payload ?? null) ? $payment->payload : (json_decode($payment->payload ?? '[]', true) ?: []));
    $dataAttr = data_get($satim, 'data.attributes', []);

    $satimOrderNumber = data_get($dataAttr, 'order_id',
                        data_get($dataAttr, 'order_number',
                        data_get($dataAttr, 'params.OrderNumber',
                        data_get($satim, 'data.id',
                        data_get($satim, 'OrderNumber',
                        ($payment->order_id ?? '—'))))));
    $satimAuthCode    = data_get($dataAttr, 'approval_code',
                        data_get($dataAttr, 'auth_code',
                        data_get($dataAttr, 'params.authorizationResponseId',
                        data_get($satim, 'approval_code',
                        data_get($satim, 'approvalCode',
                        data_get($satim, 'authorizationResponseId'))))));
    $satimAction      = data_get($dataAttr, 'action_code_description', data_get($satim, 'actionCodeDescription', '—'));
    $satimPan         = data_get($dataAttr, 'pan', data_get($satim, 'Pan'));
    $satimCardholder  = data_get($dataAttr, 'cardholderName', data_get($satim, 'cardholderName'));

    if (preg_match('/error\s*code\s*[:=]/i', (string) $satimAction)) {
        $satimAction = null;
    }
@endphp

<div class="box">
    <h2>وصل دفع إلكتروني</h2>
    <h3>ديوان المركب المتعدد الرياضات لولاية البيض</h3>

    <p class="mt"><strong>رقم الوصل المحلي:</strong> {{ $payment->order_id ?? '—' }}</p>
    <p><strong>رقم العملية:</strong> {{ $satimOrderNumber ?? '—' }}</p>
    <p><strong>رمز الموافقة:</strong> {{ $satimAuthCode ?? '—' }}</p>

    <p><strong>اسم المستفيد:</strong> {{ optional($user)->name ?? '—' }}</p>
    <p><strong>البريد الإلكتروني:</strong> {{ optional($user)->email ?? '—' }}</p>

    <p><strong>حالة العملية:</strong> {{ $satimAction ?? '—' }}</p>
    <p><strong>المبلغ:</strong> {{ number_format(($payment->amount ?? 0) / 100, 2) }} دج</p>
    <p><strong>العملة:</strong> {{ data_get($dataAttr, 'currency', data_get($satim, 'currency', '012')) }}</p>
    <p><strong>رقم البطاقة:</strong> {{ $satimPan ?? '—' }}</p>
    <p><strong>اسم حامل البطاقة:</strong> {{ $satimCardholder ?? '—' }}</p>

    @if($reservation)
        <table>
            <tr>
                <th>النشاط</th>
                <th>المنشأة</th>
                <th>المدة</th>
            </tr>
            <tr>
                <td>{{ $reservation->complexActivity->activity->title ?? '—' }}</td>
                <td>{{ $reservation->complexActivity->complex->nom ?? '—' }}</td>
                <td>{{ $reservation->season->name ?? '—' }}</td>
            </tr>
        </table>
    @endif

    <p class="mt">تم إنشاء هذا الوصل بتاريخ {{ now()->format('Y-m-d H:i') }}</p>
</div>
</body>
</html>