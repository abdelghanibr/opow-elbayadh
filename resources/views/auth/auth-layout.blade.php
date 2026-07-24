<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'تسجيل الدخول' }} - {{ config('app.name') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: "Cairo";
            background: #e8f5e9;
            direction: rtl;
            text-align: right;
        }
        .auth-box {
            background: #fff;
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }
        .btn-main {
            background: #1b5e20;
            color: #fff;
            border-radius: 10px;
            font-weight: 700;
        }
        a { text-decoration: none; }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="auth-box col-md-5">
        <h3 class="text-center mb-4">{{ $title }}</h3>

        @yield('content')

    </div>
</div>

</body>
</html>
