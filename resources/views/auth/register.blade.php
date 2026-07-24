<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء حساب جديد</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: #e8f5e9;
            padding: 35px 10px;
        }

        .register-box {
            background: #ffffff;
            width: 100%;
            max-width: 900px;
            margin: auto;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.13);
        }

        .form-label {
            font-weight: 600;
        }

        .form-error {
            color: #b71c1c;
            font-size: 0.9rem;
            margin-top: 4px;
        }

        .is-invalid {
            border-color: #e53935 !important;
        }

        .title-box img {
            max-width: 90px;
        }

        @media (max-width: 576px) {
            .register-box {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>

<div class="register-box">

    <div class="text-center mb-4 title-box">
        <img src="{{ asset('images/djs-logo.png') }}" alt="Logo">
        <h2 class="mt-2">وزارة الشباب</h2>
        <h5 class="text-secondary">منصة النشاطات الشبانية</h5>
    </div>

    <form method="POST" action="{{ route('register.post') }}">
        @csrf

        <div class="row g-4">

            <!-- Firstname -->
            <div class="col-md-6">
                <label class="form-label">الاسم</label>
                <input type="text" name="firstname"
                       value="{{ old('firstname') }}"
                       class="form-control @error('firstname') is-invalid @enderror" required>
                @error('firstname')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Lastname -->
            <div class="col-md-6">
                <label class="form-label">اللقب</label>
                <input type="text" name="lastname"
                       value="{{ old('lastname') }}"
                       class="form-control @error('lastname') is-invalid @enderror" required>
                @error('lastname')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email"
                       value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror" required>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Username -->
            <div class="col-md-6">
                <label class="form-label">اسم المستخدم</label>
                <input type="text" name="username"
                       value="{{ old('username') }}"
                       class="form-control @error('username') is-invalid @enderror" required>
                @error('username')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Complex selection -->
            <div class="col-md-6">
                <label class="form-label">اختر المجمع الرياضي</label>

                <select name="complex_id"
                        class="form-control @error('complex_id') is-invalid @enderror"
                        required>
                    <option value="">— اختر المجمع —</option>

                    @foreach($complexes as $complex)
                        <option value="{{ $complex->id }}"
                            {{ old('complex_id') == $complex->id ? 'selected' : '' }}>
                            {{ $complex->name }}
                        </option>
                    @endforeach
                </select>

                @error('complex_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="col-md-6">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="col-md-6">
                <label class="form-label">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation"
                       class="form-control" required>
            </div>

            <!-- Terms -->
            <div class="col-12">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" required>
                    أوافق على الشروط والأحكام
                </label>
            </div>

            <!-- Submit -->
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-success w-100 py-2" style="font-size: 1.1rem;">
                    تسجيل
                </button>
            </div>

        </div>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
