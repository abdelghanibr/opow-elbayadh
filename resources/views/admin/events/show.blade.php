
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $event->title }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap & Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body{
            font-family:'Cairo',sans-serif;
            background:#f6f7fb;
        }

        /* ===== HERO ===== */
        .event-hero{
            background:linear-gradient(135deg,#0a3d62,#0b5d57);
            color:#fff;
            padding:80px 0 120px;
            text-align:center;
            position:relative;
        }

        .event-image-circle{
            width:160px;
            height:160px;
            border-radius:50%;
            overflow:hidden;
            margin:0 auto;
            border:6px solid #fff;
            box-shadow:0 15px 35px rgba(0,0,0,.25);
            background:#fff;
        }
        .event-image-circle img{
            width:100%;
            height:100%;
            object-fit:cover;
        }

        .event-title{
            font-weight:900;
            margin-top:25px;
        }

        /* ===== CARD ===== */
        .event-card{
            background:#fff;
            border-radius:24px;
            box-shadow:0 18px 45px rgba(0,0,0,.12);
            padding:35px;
            margin-top:-80px;
        }

        .event-meta{
            display:flex;
            gap:20px;
            flex-wrap:wrap;
            font-size:.9rem;
            color:#6b7280;
            margin-bottom:20px;
        }

        .event-meta i{
            color:#0b5d57;
            margin-left:6px;
        }

        .event-description{
            font-size:1rem;
            line-height:1.9;
            color:#374151;
        }

        .btn-back{
            border-radius:999px;
            padding:8px 22px;
        }

        @media(max-width:576px){
            .event-hero{
                padding:60px 0 100px;
            }
            .event-card{
                padding:25px;
            }
            .event-image-circle{
                width:130px;
                height:130px;
            }
        }
    </style>
    
    @php
    $shareTitle = $event->title;
    $shareDescription = \Illuminate\Support\Str::limit(strip_tags($event->description), 160);
    $shareImage = $event->image
        ? asset($event->image)
        : asset('images/placeholder.png');
    $shareUrl = route('events.show', $event->id);
@endphp

<!-- Open Graph / Facebook -->
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $shareTitle }}">
<meta property="og:description" content="{{ $shareDescription }}">
<meta property="og:image" content="{{ $shareImage }}">
<meta property="og:url" content="{{ $shareUrl }}">
<meta property="og:site_name" content="ديوان المركب المتعدد الرياضات - البيض">

<!-- Twitter (اختياري لكنه جيد) -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $shareTitle }}">
<meta name="twitter:description" content="{{ $shareDescription }}">
<meta name="twitter:image" content="{{ $shareImage }}">
    
    
</head>
<body>

@php
    $storageUrl = app()->environment('local')
        ? '/storage'
        : rtrim(env('PUBLIC_STORAGE_URL'), '/');
@endphp

<!-- HERO -->
<section class="event-hero">
    <div class="container">

       @if($event->image)
    <div class="event-image-circle">
        <img src="{{ asset($event->image) }}"
             alt="{{ $event->title }}">
    </div>
@else
    <div class="event-image-circle">
        <img src="{{ asset('images/placeholder.png') }}"
             alt="Event">
    </div>
@endif


        <h1 class="event-title">{{ $event->title }}</h1>
    </div>
</section>

<!-- CONTENT -->
<div class="container">
    <div class="event-card">

        <div class="event-meta">
            <div>
                <i class="fa-solid fa-calendar-days"></i>
                {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}
            </div>

            <div>
                <i class="fa-solid fa-building-columns"></i>
                وزارة الرياضة – ولاية البيض
            </div>
        </div>

        <div class="event-description">
            {!! nl2br(e($event->description)) !!}
        </div>

        <div class="mt-4">
            <a href="{{ url('/') }}" class="btn btn-secondary btn-back">
                ← الرجوع إلى الصفحة الرئيسية
            </a>
        </div>

    </div>
</div>

</body>
</html>
