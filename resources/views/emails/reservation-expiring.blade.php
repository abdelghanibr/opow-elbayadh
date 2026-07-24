<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تنبيه انتهاء الحجز</title>
</head>

<body style="
    margin:0;
    padding:0;
    background-color:#f4f6f8;
    font-family: Tahoma, Arial, sans-serif;
    direction: rtl;
">

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" style="padding:30px 15px">

            <!-- Card -->
            <table width="600" cellpadding="0" cellspacing="0" style="
                background:#ffffff;
                border-radius:10px;
                box-shadow:0 6px 18px rgba(0,0,0,0.08);
                overflow:hidden;
            ">

                <!-- Header -->
           <tr>
    <td style="
        background: linear-gradient(135deg, #0a4f88, #0a8a67);
        color: #ffffff;
        padding: 24px 20px;
        text-align: center;
        font-family: Arial, sans-serif;
    ">
        <div style="
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 6px;
        ">
            ديوان المركب المتعدد الرياضات لولاية البيض
        </div>

        <div style="
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 0.5px;
        ">
            ⏰ تنبيه انتهاء الحجز
        </div>
    </td>
</tr>


                <!-- Content -->
                <tr>
                    <td style="padding:25px; color:#333; font-size:15px; line-height:1.9">

                        <p>
                            مرحبًا
                            <strong>{{ $reservation->user->name ?? 'عميلنا الكريم' }}</strong>،
                        </p>

                        <p>
                            نود إعلامك أن حجزك سينتهي بتاريخ:
                        </p>

                        <p style="
                            background:#f1f8ff;
                            border-right:4px solid #0a6cff;
                            padding:12px;
                            font-size:16px;
                            font-weight:bold;
                            text-align:center;
                        ">
                            📅 {{ \Carbon\Carbon::parse($reservation->end_date)->format('Y-m-d') }}
                        </p>

                        <p>
                            ننصحك بتجديد الحجز قبل انتهاء المدة لتفادي توقف الخدمة
                            وضمان الاستفادة المستمرة من النشاط.
                        </p>

                        <!-- CTA -->
                        <div style="text-align:center; margin:30px 0">
                            <a href="{{ url('/reservations') }}"
                               style="
                                   background:#0a8a67;
                                   color:#ffffff;
                                   text-decoration:none;
                                   padding:12px 26px;
                                   border-radius:6px;
                                   font-weight:bold;
                                   display:inline-block;
                               ">
                                🔁 تجديد الحجز الآن
                            </a>
                        </div>

                        <p>
                            في حال كانت لديك أي استفسارات، لا تتردد في التواصل معنا.
                        </p>

                        <p style="margin-top:30px">
                            مع فائق الاحترام 🌟<br>
                            <strong>{{ config('app.name') }}</strong>
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="
                        background:#f0f0f0;
                        padding:15px;
                        text-align:center;
                        font-size:12px;
                        color:#777;
                    ">
                        هذا البريد أُرسل تلقائيًا، يرجى عدم الرد عليه
                        <br>
                        © {{ date('Y') }} {{ config('app.name') }}
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
