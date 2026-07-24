@extends('layouts.app')

@section('content')
@php
    $person  = $dossier->person ?? null;
    $user    = $person?->user;
    $complex = $user?->complex;

    $fullName = trim(($person->firstname ?? '') . ' ' . ($person->lastname ?? ''));
    $birthDate = !empty($person?->birth_date) ? \Carbon\Carbon::parse($person->birth_date)->format('Y/m/d') : '.........................';

    // adapte ces champs selon tes colonnes réelles
    $fatherName     = $person->tuteur_fullname ?? $person->father_name ?? '.........................';
    $address        = $person->address ?? '.........................';
    $phone          = $person->phone ?? '.........................';
    $email          = $user?->email ?? '.........................';
    $profession     = $person->profession ?? $person->job ?? '.........................';
    $bloodType      = $person->blood_type ?? '.........................';

    $idNumber       = $person->nin ?? $person->id_number ?? '.........................';
    $idIssueDate    = !empty($person?->id_issue_date) ? \Carbon\Carbon::parse($person->id_issue_date)->format('Y/m/d') : '.........................';
    $idIssuePlace   = $person->id_issue_place ?? '.........................';
 $latestSeason = \App\Models\Season::where('type_season', 'season')
        ->orderByDesc('date_debut')
        ->first();

    $seasonLabel = $latestSeason->name ?? '2025/2026';
    $photoPath = !empty($person?->photo) ? asset($person->photo) : asset('images/avatar.png');
@endphp

<style>
    .formulaire-wrapper{
        direction: rtl;
        font-family: 'Cairo', sans-serif;
        color:#111;
    }

    .page-a4{
        width: 210mm;
        min-height: 297mm;
        margin: 10px auto;
        background: #fff;
        padding: 14mm 12mm;
        box-shadow: 0 0 6px rgba(0,0,0,.08);
        position: relative;
    }

    .page-break{
        page-break-after: always;
    }

    .doc-title{
        text-align:center;
        font-size: 28px;
        font-weight: 800;
        text-decoration: underline;
        margin-bottom: 24px;
    }

    .season-title{
        text-align:center;
        font-size: 24px;
        font-weight: 800;
        text-decoration: underline;
        margin-bottom: 22px;
    }

    .line{
        display:inline-block;
        border-bottom:1px dotted #222;
        min-width:180px;
        padding:0 4px 2px;
        line-height:1.6;
        font-weight:600;
    }

    .line-sm{ min-width:120px; }
    .line-md{ min-width:180px; }
    .line-lg{ min-width:280px; }
    .line-xl{ min-width:420px; }

    .paragraph{
        font-size: 22px;
        line-height: 2.1;
        margin-bottom: 12px;
    }

    .section-subtitle{
        text-align:center;
        font-size: 26px;
        font-weight: 800;
        text-decoration: underline;
        margin: 36px 0 20px;
    }

    .rules-list{
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .rules-list li{
        font-size: 21px;
        line-height: 2.15;
        margin-bottom: 6px;
    }

    .footer-sign{
        margin-top: 80px;
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        font-size:22px;
        font-weight:700;
    }

    .season-list{
        list-style: disc;
        padding-right: 28px;
        margin: 0 0 24px 0;
    }

    .season-list li{
        font-size: 22px;
        line-height: 2.15;
        margin-bottom: 8px;
    }

    .doctor-box{
        margin-top: 32px;
    }

    .doctor-title{
        text-align:center;
        font-size: 24px;
        font-weight: 800;
        text-decoration: underline;
        margin: 22px 0;
    }

    .stamp-row{
        margin-top: 50px;
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:20px;
    }

    .stamp-box{
        width: 260px;
        height: 120px;
        border: 1px dashed #bbb;
    }

    .photo-print{
        position:absolute;
        top: 18mm;
        left: 12mm;
        width: 28mm;
        height: 36mm;
        border:1px solid #bbb;
        object-fit:cover;
        background:#fff;
    }

    .print-actions{
        text-align:center;
        margin: 16px 0 24px;
    }

    @media print{
        body *{
            visibility:hidden;
        }

        #printArea, #printArea *{
            visibility:visible;
        }

        #printArea{
            position:absolute;
            top:0;
            left:0;
            width:100%;
        }

        .no-print{
            display:none !important;
        }

        .page-a4{
            box-shadow:none;
            margin:0;
            width:100%;
            min-height:auto;
        }
    }

    @media (max-width: 768px){
        .page-a4{
            width:100%;
            min-height:auto;
            padding:20px 14px;
        }

        .doc-title,
        .season-title,
        .section-subtitle{
            font-size: 22px;
        }

        .paragraph,
        .rules-list li,
        .season-list li{
            font-size: 17px;
            line-height: 2;
        }

        .photo-print{
            width:80px;
            height:100px;
            top:16px;
            left:16px;
        }

        .footer-sign,
        .stamp-row{
            font-size:18px;
        }

        .line-xl,
        .line-lg,
        .line-md,
        .line-sm{
            min-width: auto;
            width: auto;
            max-width: 100%;
        }
    }
</style>

<div class="container py-3 formulaire-wrapper">
    <div id="printArea">

        {{-- PAGE 1 --}}
        <div class="page-a4 page-break">
            <img src="{{ $photoPath }}" class="photo-print" alt="photo">

            <div class="doc-title">الالتزام</div>

            <div class="paragraph">
                أنا الممضي أسفله السيد(ة)
                <span class="line line-lg">{{ $fullName ?: '.........................' }}</span>
            </div>

            <div class="paragraph">
                الحامل لبطاقة التعريف الوطنية أو رخصة السياقة رقم
                <span class="line line-md">{{ $idNumber }}</span>
            </div>

            <div class="paragraph">
                الصادرة بتاريخ
                <span class="line line-sm">{{ $idIssueDate }}</span>
                عن دائرة
                <span class="line line-sm">{{ $idIssuePlace }}</span>
            </div>

            <div class="section-subtitle">أتعهد وألتزم</div>

            <ul class="rules-list">
                <li>1- إحضار بطاقة الانخراط إجباري في حالة عدم إحضارها يمنع المشترك من الدخول مهما يكن.</li>
                <li>2- عدم تغيير الفوج واحترام التوقيت.</li>
                <li>3- الخروج من المسبح فور سماع الجرس (انتهاء الحصة).</li>
                <li>4- عدم التسبب في مضايقة السباحين داخل المسبح.</li>
                <li>5- احترام النظام الداخلي للمنشأة.</li>
                <li>6- عدم القيام بعمل تخريبي يمس بحرمة المنشأة.</li>
                <li>7- تجديد البطاقة خلال مدة أقصاها 03 أيام بعد انقضائها للمحافظة على نفس الفوج.</li>
                <li>ملاحظة هامة: عدم جلب الأشياء الثمينة (الأموال، الذهب، الهاتف النقال...) والإدارة غير مسؤولة عن ضياعها.</li>
                <li>8- تسترجع الإدارة الحصص الضائعة في حال ما إذا توقف نشاط الوحدة فقط.</li>
                <li>* في حالة عدم الالتزام بهذه البنود فإنه يتم سحب البطاقة من المشترك دون تعويض.</li>
            </ul>

            <div class="footer-sign">
                <div>المعني بالأمر</div>
                <div>تأشيرة البلدية</div>
            </div>

            <div class="paragraph" style="margin-top:90px; text-align:center;">
                حرر في
                <span class="line line-sm">....................</span>
                بتاريخ:
                <span class="line line-sm">{{ now()->format('Y/m/d') }}</span>
            </div>
        </div>

        {{-- PAGE 2 --}}
        <div class="page-a4">
           <div class="season-title">ملأ استمارة  {{ $seasonLabel }}</div>

            <ul class="season-list">
                <li>
                    الاسم
                    <span class="line line-md">{{ $person->firstname ?? '.........................' }}</span>
                    اللقب
                    <span class="line line-md">{{ $person->lastname ?? '.........................' }}</span>
                </li>

                <li>
                    تاريخ الازدياد:
                    <span class="line line-xl">{{ $birthDate }}</span>
                </li>

                <li>
                    اسم الأب:
                    <span class="line line-xl">{{ $fatherName }}</span>
                </li>

                <li>
                    العنوان:
                    <span class="line line-xl">{{ $address }}</span>
                </li>

                <li>
                    المهنة:
                    <span class="line line-xl">{{ $profession }}</span>
                </li>

                <li>
                    الهاتف:
                    <span class="line line-xl">{{ $phone }}</span>
                </li>

                <li>
                    فصيلة الدم:
                    <span class="line line-xl">{{ $bloodType }}</span>
                </li>

                <li>
                    البريد الإلكتروني:
                    <span class="line line-xl">{{ $email }}</span>
                </li>
            </ul>

            <div class="doctor-box">
                <div class="doctor-title">شهادة صدرية طبية وعامة من مؤسسة عمومية</div>

                <div class="paragraph">
                    أنا الممضي أسفله الطبيب(ة):
                    <span class="line line-lg">{{ $doctorName ?? '.........................' }}</span>
                </div>

                <div class="paragraph">
                    أشهد أن المذكور(ة) أعلاه يتمتع بصحة جيدة ولم أكشف عن أي أعراض تمنعه من ممارسة رياضة السباحة.
                </div>

                <div class="stamp-row">
                    <div>
                        <div class="paragraph" style="font-weight:800;">ختم الطبيب</div>
                        <div class="stamp-box"></div>
                    </div>

                    <div style="flex:1; text-align:center; padding-top:48px;">
                        <div class="paragraph">
                            يوم:
                            <span class="line line-md">.........................</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="paragraph" style="margin-top:40px; text-align:center;">
                تم إنشاء هذه الوثيقة إلكترونيًا عبر منصة {{ config('app.name') }} بتاريخ {{ now()->format('Y/m/d H:i') }}
            </div>
        </div>
    </div>

    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ طباعة الاستمارة
        </button>
    </div>
</div>
@endsection