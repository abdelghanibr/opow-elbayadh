<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>التحقق من البطاقة</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Cairo", sans-serif;
            background: #eef2f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: rtl;
            padding: 20px;
        }
        .box {
            background: #fff;
            border: 2px solid #1b5e20;
            border-radius: 22px;
            padding: 26px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 12px 34px rgba(0,0,0,.10);
        }
        .box h1 { font-size: 17px; font-weight: 800; color: #10330f; text-align: center; }
        .box .status {
            text-align: center;
            font-size: 15px;
            font-weight: 800;
            padding: 8px 12px;
            border-radius: 12px;
            margin: 14px 0;
        }
        .st-valid { background: #e6f4e8; color: #1b5e20; }
        .st-expired { background: #fef3cd; color: #b45309; }
        .st-stopped { background: #fdecec; color: #b91c1c; }
        .row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px dashed #dbe5dc;
            font-size: 13px;
        }
        .row .k { color: #6b7f6d; font-weight: 600; }
        .row .v { font-weight: 800; color: #0d3f12; }
        .foot { margin-top: 16px; text-align: center; font-size: 11px; color: #8aa08d; }
        .shield { text-align: center; font-size: 26px; }
    </style>
</head>
<body>
    <div class="box">
        <div class="shield">🛡️</div>
        <h1>التحقق من بطاقة اللاعب</h1>

        @php
            $stClass = $card['canIssue'] ? 'st-valid'
                : ($card['status'] === 'موقوفة' ? 'st-stopped' : 'st-expired');
        @endphp
        <div class="status {{ $stClass }}">{{ $card['status'] === 'سارية' ? '✅ سارية' : ($card['status'] === 'موقوفة' ? '⛔ موقوفة' : '⏳ منتهية') }}</div>

        <div class="row"><span class="k">اللقب</span><span class="v">{{ $card['lastname'] }}</span></div>
        <div class="row"><span class="k">الاسم</span><span class="v">{{ $card['firstname'] }}</span></div>
        <div class="row"><span class="k">الفئة</span><span class="v">{{ $card['category'] ?: '—' }}</span></div>
        <div class="row"><span class="k">النادي</span><span class="v">{{ $card['club'] }}</span></div>
        <div class="row"><span class="k">المنشأة الرياضية</span><span class="v">{{ $card['complex'] }}</span></div>
        <div class="row"><span class="k">الموسم الرياضي</span><span class="v">{{ $card['seasonLabel'] }}</span></div>
        <div class="row"><span class="k">رقم البطاقة</span><span class="v">{{ $card['cardNumber'] }}</span></div>

        <div class="foot">هذه الصفحة تعرض معلومات عامة للتحقق فقط • OPOW البيض</div>
    </div>
</body>
</html>