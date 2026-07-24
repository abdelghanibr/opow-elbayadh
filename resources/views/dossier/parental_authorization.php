@extends('layouts.app')

@section('content')
@php
    $person  = $dossier->person ?? null;
    $user    = $person?->user;

    // Enfant
    $childFullName   = trim(($person->firstname ?? '') . ' ' . ($person->lastname ?? ''));
    $childBirthDate  = !empty($person?->birth_date)
        ? \Carbon\Carbon::parse($person->birth_date)->format('Y/m/d')
        : '................................';

    // Parent / tuteur
    $parentName      = $person->tuteur_fullname ?? $person->parent_name ?? '................................';
    $parentBirthDate = !empty($person?->tuteur_birth_date)
        ? \Carbon\Carbon::parse($person->tuteur_birth_date)->format('Y/m/d')
        : '................................';
    $parentAddress   = $person->address ?? '................................';
    $parentIdNumber  = $person->tuteur_nin ?? $person->nin ?? $person->id_number ?? '................................';


 $latestSeason = \App\Models\seasons::where('type_season', 'season')
        ->orderByDesc('date_debut')
        ->first();

    $seasonLabel = $latestSeason ;
   

    // Date / lieu
   
    $cityName        = $person->city ?? 'البيض';
    $todayDate       = now()->format('Y/m/d');
@endphp


 <style>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap');

    .capture-wrapper{
        direction: rtl;
        font-family: 'Cairo', sans-serif;
        color: #111;
    }

    .a4-page{
        width: 210mm;
        min-height: 297mm;
        margin: 10px auto;
        background: #fff;
        padding: 18mm 16mm;
        box-shadow: 0 0 8px rgba(0,0,0,.08);
    }

    .doc-title{
        text-align: center;
        font-size: 24px;
        font-weight: 700;
        text-decoration: underline;
        margin-bottom: 30px;
    }

    .paragraph{
        font-size: 18px;
        line-height: 2.0;
        margin-bottom: 10px;
    }

    .line-fill{
        display: inline-block;
        border-bottom: 1px dotted #222;
        min-width: 260px;
        padding: 0 6px 2px;
        font-weight: 600;
        line-height: 1.8;
    }

    .line-sm{ min-width: 140px; }
    .line-md{ min-width: 220px; }
    .line-lg{ min-width: 320px; }
    .line-xl{ min-width: 480px; }

    .center-title{
        text-align: center;
        font-size: 22px;
        font-weight: 700;
        text-decoration: underline;
        margin: 26px 0 18px;
    }

    .rules{
        margin-top: 10px;
        padding-right: 18px;
    }

    .rules li{
        font-size: 19px;
        line-height: 2;
        margin-bottom: 6px;
    }

    .note{
        font-size: 19px;
        line-height: 2;
        margin-top: 8px;
    }

    .signature-row{
        margin-top: 80px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 20px;
        font-weight: 600;
    }

    .bottom-row{
        margin-top: 90px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 20px;
    }

    .print-actions{
        text-align: center;
        margin: 20px 0;
    }

    @media print{
        body *{
            visibility: hidden;
        }

        #printArea, #printArea *{
            visibility: visible;
        }

        #printArea{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        .no-print{
            display: none !important;
        }

        .a4-page{
            width: 100%;
            min-height: auto;
            margin: 0;
            box-shadow: none;
        }
    }

    @media (max-width: 768px){
        .a4-page{
            width: 100%;
            min-height: auto;
            padding: 20px 12px;
        }

        .doc-title{
            font-size: 20px;
        }

        .paragraph,
        .rules li,
        .note,
        .signature-row,
        .bottom-row{
            font-size: 16px;
        }

        .line-fill,
        .line-sm,
        .line-md,
        .line-lg,
        .line-xl{
            min-width: auto;
            width: auto;
            max-width: 100%;
        }
    }
</style>
</style>


<div class="container py-3 capture-wrapper">
    <div id="printArea">
        <div class="a4-page">

            <div class="doc-title">تصريح أبوي للسباحة الترفيهية</div>

            <div class="paragraph">
                أنا الممضي أسفله السيد/
                <span class="line-fill line-xl">{{ $parentName }}</span>
            </div>

            <div class="paragraph">
                المولود في
                <span class="line-fill line-lg">{{ $parentBirthDate }}</span>
            </div>

            <div class="paragraph">
                ساكن بـ
                <span class="line-fill line-xl">{{ $parentAddress }}</span>
            </div>

            <div class="paragraph">
                الحامل لبطاقة التعريف الوطنية أو رخصة السياقة رقم:
                <span class="line-fill line-lg">{{ $parentIdNumber }}</span>
            </div>

            <div class="paragraph">
                أسمح لابني/ابنتي /
                <span class="line-fill line-lg">{{ $childFullName ?: '................................' }}</span>
            </div>

            <div class="paragraph">
                المولود(ة) في
                <span class="line-fill line-lg">{{ $childBirthDate }}</span>
            </div>

            <div class="paragraph">
                بالتسجيل في السباحة الترفيهية التابعة للمسبح النصف أولمبي ببلدية الشهيد محمود مغلاوي
                  -للموسم الرياضي
                <strong>{{ $seasonLabel }}</strong>
            </div>

            <div class="center-title">أتعهد وألتزم</div>

            <ol class="rules">
                <li>إحضار بطاقة الانخراط إجباري، وفي حالة عدم إحضارها يمنع المشترك من الدخول مهما يكن.</li>
                <li>عدم تغيير الفوج واحترام التوقيت.</li>
                <li>الخروج من المسبح فور سماع الجرس (انتهاء الحصة).</li>
                <li>عدم التسبب في مضايقة السباحين داخل المسبح.</li>
                <li>احترام النظام الداخلي للمنشأة.</li>
                <li>عدم القيام بعمل تخريبي يمس بحرمة المنشأة.</li>
                <li>تجديد البطاقة خلال مدة أقصاها 03 أيام بعد انقضائها للمحافظة على نفس الفوج.</li>
            </ol>

            <div class="note">
                <strong>ملاحظة هامة:</strong>
                عدم جلب الأشياء الثمينة (الأموال، الذهب، الهاتف...)، والإدارة غير مسؤولة عن ضياعها.
            </div>

            <div class="note">
                * في حالة عدم الالتزام بهذه البنود فإنه يتم سحب البطاقة من المشترك دون تعويض.
            </div>

            <div class="signature-row">
                <div>تأشيرة البلدية</div>
                <div>إمضاء الولي</div>
            </div>

            <div class="bottom-row">
                <div>{{ $cityName }}</div>
                <div>
                    في:
                    <span class="line-fill line-md">{{ $todayDate }}</span>
                </div>
            </div>

        </div>
    </div>

    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ طباعة التصريح</button>
    </div>
</div>
@endsection