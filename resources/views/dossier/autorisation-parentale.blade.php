@extends('layouts.app')

@section('content')

@php
    $person  = $dossier->person ?? null;
    $user    = $person?->user;


    $complex = $user?->complex;

    // Activité : adapte seulement la source selon ta vraie relation
    $activity = $dossier->activity ?? $person?->activity ?? null;

    $activityName = $activity?->name_ar
        ?? $activity?->name
        ?? $activity?->title
        ?? 'السباحة الترفيهية';

    $complexLabel = $complex?->full_name
        ?? $complex?->name_ar
        ?? $complex?->name
        ?? $complex?->nom
        ?? 'المسبح النصف أولمبي ببلدية الشهيد محمود مغلاوي';
 
 
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

    // Saison
    $latestSeason = \App\Models\Season::where('type_season', 'season')
        ->orderByDesc('date_debut')
        ->first();

    $seasonLabel = $latestSeason->name ?? '2025/2026';

    // Date / lieu
    $cityName  = $person->city ?? 'البيض';
    $todayDate = now()->format('Y/m/d');
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap');

    .capture-wrapper{
        direction: rtl;
        font-family: 'Cairo', sans-serif;
        color: #111;
    }

    .a4-page{
        width: 190mm;
        height: 277mm;
        margin: 10px auto;
        background: #fff;
        padding: 0;
        box-shadow: 0 0 8px rgba(0,0,0,.08);
        overflow: hidden;
        box-sizing: border-box;
    }

    .doc-title{
        text-align: center;
        font-size: 22px;
        font-weight: 700;
        text-decoration: underline;
        margin-bottom: 20px;
    }

    .paragraph{
        font-size: 17px;
        line-height: 1.9;
        margin-bottom: 8px;
    }

    .line-fill{
        display: inline-block;
        border-bottom: 1px dotted #222;
        min-width: 260px;
        padding: 0 6px 2px;
        font-weight: 600;
        line-height: 1.7;
    }

    .line-sm{ min-width: 140px; }
    .line-md{ min-width: 220px; }
    .line-lg{ min-width: 320px; }
    .line-xl{ min-width: 480px; }

    .center-title{
        text-align: center;
        font-size: 19px;
        font-weight: 700;
        text-decoration: underline;
        margin: 16px 0 12px;
    }

    .rules{
        margin-top: 6px;
        margin-bottom: 8px;
        padding-right: 18px;
    }

    .rules li{
        font-size: 15px;
        line-height: 1.7;
        margin-bottom: 4px;
    }

    .note{
        font-size: 14px;
        line-height: 1.7;
        margin-top: 6px;
    }

    .signature-row{
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 18px;
        font-weight: 600;
    }

    .bottom-row{
        margin-top: 50px;
        text-align: left;
        direction: ltr;
        font-size: 17px;
        font-weight: 600;
    }

    .bottom-row span{
        display: inline-block;
    }

    .print-actions{
        text-align: center;
        margin: 20px 0;
    }

    @media print{
        @page{
            size: A4;
            margin: 1cm;
        }

        html, body{
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }

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
            width: 190mm;
        }

        .no-print{
            display: none !important;
        }

        .a4-page{
            width: 190mm;
            height: 277mm;
            margin: 0;
            padding: 0;
            box-shadow: none;
            overflow: hidden;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
    }

    @media (max-width: 768px){
        .a4-page{
            width: 100%;
            height: auto;
            padding: 0;
            overflow: visible;
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


<div class="container py-3 capture-wrapper">
    <div id="printArea">
        <div class="a4-page">

             <div class="doc-title">تصريح أبوي</div>

            <div class="paragraph">
                أنا الممضي أسفله السيد/
                <span class="line-fill line-xl">{{ $parentName }}</span>
            </div>

            <div class="paragraph">
                المولود في
                <span class="line-fill line-lg">{{ $parentBirthDate }}</span>
          
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
    بالتسجيل في
    <span class="line-fill line-md">{{ $activityName }}</span>
    التابعة لـ
    <span class="line-fill line-lg">{{ $complexLabel }}</span>
    
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

     <div class="bottom-row" >  
    <div  >
        <span >{{ $cityName }} في: {{ $todayDate }}</span>
    </div>
    </div>
    


        
    </div>

    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ طباعة التصريح</button>
    </div>
</div>
@endsection