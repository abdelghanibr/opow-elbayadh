@extends('layouts.app')

@section('content')

<style>
    body { font-family: "Cairo", sans-serif !important; }

    .match-box {
        background: #ffffff;
        border-radius: 22px;
        padding: 25px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        margin-bottom: 25px;
        transition: 0.3s ease;
        border: 1px solid #eef1f5;
    }

    .match-box:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(0,0,0,0.12);
    }

    .team-logo {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.18);
    }

    .team-name {
        font-size: 19px;
        font-weight: 800;
        margin-top: 10px;
        color: #0a4f99;
    }

    .vs-text {
        font-size: 32px;
        font-weight: 900;
        color: #d10d0d;
        margin: 10px 0;
    }

    .match-info {
        font-size: 15px;
        color: #555;
        line-height: 1.8;
    }

    .reserve-btn {
        background: linear-gradient(135deg,#0a4f88,#1ba3d6);
        padding: 12px 35px;
        border-radius: 40px;
        color: #fff;
        font-weight: 700;
        border: none;
        transition: 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .reserve-btn:hover {
        background: linear-gradient(135deg,#c40909,#ff1a1a);
        transform: scale(1.07);
        box-shadow: 0 10px 30px rgba(255,0,0,0.3);
    }

    @media(max-width: 768px) {
        .team-logo { width: 70px; height: 70px; }
        .vs-text { font-size: 26px; }
    }
</style>


<div class="container py-5" style="direction: rtl; text-align:right;">

    <h2 class="fw-bold text-center mb-5" style="color:#0a4f88;">
        🎟️ المباريات المتاحة لحجز التذاكر
    </h2>

    @foreach($matches as $m)
        <div class="match-box">

            <div class="row text-center align-items-center">

                <!-- الفريق المستضيف -->
                <div class="col-md-4">
                    <img src="{{ asset($m->homeTeam->logo) }}" class="team-logo">
                    <div class="team-name">{{ $m->homeTeam->name }}</div>
                </div>

                <!-- بيانات المباراة -->
                <div class="col-md-4">
                    <div class="vs-text">VS</div>

                    <div class="match-info">
                        <div>📅 {{ $m->match_date }}</div>
                        <div>⏰ {{ $m->match_time }}</div>
                        <div>🏟 {{ $m->complex->nom }}</div>
                    </div>

                    <a href="{{ route('tickets.select-seat', $m->id) }}" class="reserve-btn mt-3">
    احجز الآن 🎟
</a>
                </div>

                <!-- الفريق الضيف -->
                <div class="col-md-4">
                    <img src="{{ asset($m->awayTeam->logo) }}" class="team-logo">
                    <div class="team-name">{{ $m->awayTeam->name }}</div>
                </div>

            </div>

        </div>
    @endforeach

</div>
@endsection
