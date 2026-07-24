<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تم إنشاء الحساب بنجاح</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Cairo Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #009ffd, #2a2a72);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        .card-box {
            background: white;
            width: 95%;
            max-width: 650px;
            padding: 45px;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .icon {
            font-size: 70px;
            color: #6a0dad;
            margin-bottom: 15px;
        }

        h2 {
            font-weight: 900;
            margin-bottom: 8px;
            font-size: 28px;
        }

        p {
            color: #555;
            margin-bottom: 25px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }

        .btn-green {
            background: #2e7d32;
            color: white;
        }

        .btn-blue {
            background: #00c2ff;
            color: white;
        }

        .btn:hover {
            opacity: .9;
        }
    </style>

</head>

<body>

<div class="card-box">

    <div class="icon">✔</div>

    <h2>
        أهلاً بك، {{ $name }}
    </h2>

    <p>تم إنشاء حسابك بنجاح.</p>
    <p>لقد قمنا بإرسال رابط تأكيد إلى بريدك الإلكتروني. يرجى التحقق من بريدك لتفعيل الحساب.</p>

    <a href="https://mail.google.com" target="_blank" class="btn btn-green">
        الذهاب إلى صفحة تأكيد البريد
    </a>

    <a href="{{ url('/') }}" class="btn btn-blue">
        الذهاب إلى الصفحة الرئيسية
    </a>

</div>

</body>
</html>
