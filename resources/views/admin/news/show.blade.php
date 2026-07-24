<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $news->title }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Fonts & Bootstrap --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body{
            font-family:'Cairo',sans-serif;
            background:#f6f7fb;
        }

        /* ===== HERO ===== */
        .news-hero{
            background:linear-gradient(135deg,#1e40af,#0ea5a4);
            color:#fff;
            padding:80px 0 120px;
            text-align:center;
            position:relative;
        }

        .news-image-circle{
            width:160px;
            height:160px;
            border-radius:50%;
            overflow:hidden;
            margin:0 auto;
            border:6px solid #fff;
            box-shadow:0 15px 35px rgba(0,0,0,.25);
            background:#fff;
        }
        .news-image-circle img{
            width:100%;
            height:100%;
            object-fit:cover;
        }

        .news-title{
            font-weight:900;
            margin-top:25px;
        }

        /* ===== CARD ===== */
        .news-card{
            background:#fff;
            border-radius:24px;
            box-shadow:0 18px 45px rgba(0,0,0,.12);
            padding:35px;
            margin-top:-80px;
        }

        .news-meta{
            display:flex;
            gap:20px;
            flex-wrap:wrap;
            font-size:.9rem;
            color:#6b7280;
            margin-bottom:20px;
        }

        .news-meta i{
            color:#0ea5a4;
            margin-left:6px;
        }

        .news-content{
            font-size:1rem;
            line-height:1.9;
            color:#374151;
        }

        .btn-back{
            border-radius:999px;
            padding:8px 22px;
        }

        @media(max-width:576px){
            .news-hero{
                padding:60px 0 100px;
            }
            .news-card{
                padding:25px;
            }
            .news-image-circle{
                width:130px;
                height:130px;
            }
        }
    </style>
</head>
<body>

@php
    $storageUrl = app()->environment('local')
        ? '/storage'
        : rtrim(env('PUBLIC_STORAGE_URL'), '/');
@endphp

<!-- HERO -->
<section class="news-hero">
    <div class="container">

       @if($news->image)
    <div class="news-image-circle">
        <img src="{{ asset($news->image) }}"
             alt="{{ $news->title }}">
    </div>
@else
    <div class="news-image-circle">
        <img src="{{ asset('images/placeholder.png') }}"
             alt="News">
    </div>
@endif

        <h1 class="news-title">{{ $news->title }}</h1>
    </div>
</section>

<!-- CONTENT -->
<div class="container">
    <div class="news-card">

        <div class="news-meta">
            <div>
                <i class="fa-solid fa-calendar-days"></i>
                {{ $news->created_at->format('d/m/Y') }}
            </div>

            <div>
                <i class="fa-solid fa-building-columns"></i>
                وزارة الرياضة – ولاية البيض
            </div>
        </div>

        <div class="news-content">
            {!! nl2br(e($news->content)) !!}
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
