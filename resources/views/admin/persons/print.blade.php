<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>📄 قائمة المؤمنين</title>

    {{-- خط Cairo --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Cairo", sans-serif;
            margin: 40px;
            direction: rtl;
            text-align: right;
            color: #000;
        }

        .header-box {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-box h2,
        .header-box h3 {
            margin: 5px 0;
        }

        .logo {
            width: 90px;
            margin-bottom: 10px;
        }

        .info-box {
            margin-top: 10px;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #444;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #efefef;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            text-align: center;
            color: #555;
        }
    </style>
</head>

<body>

@php
    $complexName = $persons->first()->user->complex->nom ?? '—';
@endphp

<!-- 🔰 Header -->
<div class="header-box">
    <img src="{{ asset('images/djs-logo.png') }}" class="logo">

    <h2>ديوان المركب المتعدد الرياضات</h2>
    <h3>ولاية البيض – قائمة المؤمنين</h3>

    <div class="info-box">
        <strong>المنشأة:</strong> {{ $complexName }}
        &nbsp; | &nbsp;
        <strong>تاريخ الطباعة:</strong> {{ date('Y-m-d') }}
    </div>
</div>

<!-- 📋 Table -->
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الاسم</th>
            <th>اللقب</th>
            <th>اسم الأب</th>
            <th>العنوان</th>
            <th>تاريخ الازدياد</th>
        </tr>
    </thead>

    <tbody>
        @foreach($persons as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->firstname }}</td>
                <td>{{ $p->lastname }}</td>
                <td>{{ $p->tuteur_fullname ?? '—' }}</td>
                <td>{{ $p->address ?? '—' }}</td>
                <td>
                    {{ $p->birth_date 
                        ? \Carbon\Carbon::parse($p->birth_date)->format('Y-m-d') 
                        : '—' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- 🖨 Footer -->
<div class="footer">
    تم إنشاء هذه الوثيقة إلكترونيًا من منصة <strong>{{ config('app.name') }}</strong><br>
    التاريخ والوقت: {{ date('Y-m-d H:i') }}
</div>

<script>
    window.print();
</script>

</body>
</html>
