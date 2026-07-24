@extends('layouts.app')

@push('css')

<style>

/* ===== إعدادات عامة ===== */
body {
    background: #f4f8fc;
    font-family: 'Cairo', sans-serif;
}

/* ===== دائرة الصورة — أكبر + ظل حديث ===== */
.complex-img-circle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    padding: 6px;
    background: linear-gradient(135deg, #0066b2, #00a3e0);
    margin: -70px auto 15px auto;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 
        0 12px 25px rgba(0, 0, 0, 0.20),
        0 0 0 6px rgba(0, 163, 224, 0.25); /* توهج جميل */
}

.complex-img-circle img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #ffffff;
}

/* ===== بطاقة المركب — أكبر + تصميم فخم ===== */
.complex-mini-card {
    position: relative;
    min-height: 300px;
    padding-top: 80px;
    padding-bottom: 30px;
    border-radius: 22px;
    background: #ffffff;
    border: none;
    box-shadow: 
        0 8px 25px rgba(0,0,0,0.10),
        0 6px 12px rgba(0,0,0,0.05);
    text-align: center;
    transition: transform 0.35s ease, box-shadow 0.35s ease;
}

.complex-mini-card:hover {
    transform: translateY(-10px);
    box-shadow: 
        0 20px 35px rgba(0,0,0,0.18),
        0 10px 15px rgba(0,0,0,0.08);
}

/* ===== العنوان ===== */
.complex-title {
    font-weight: 900;
    margin-top: 12px;
    font-size: 1.25rem;
    color: #0a3d62;
}

/* ===== العنوان الثانوي ===== */
.complex-address {
    font-size: 0.95rem;
    color: #6c7a89;
    margin-bottom: 18px;
}

/* ===== زر التسجيل ===== */
.btn-register {
    background: linear-gradient(135deg, #00b894, #00cec9);
    border: none;
    color: white;
    font-weight: 700;
    border-radius: 10px;
    padding: 12px 0;
    font-size: 1rem;
    letter-spacing: 0.5px;
    width: 70%; /* 👈 عرض أصغر */
    margin: 5 auto; /* 👈 تمركز الزر */
    display: block;
    transition: all 0.25s ease;
}

.btn-register:hover {
    background: linear-gradient(135deg, #00997d, #00b3b0);
    box-shadow: 0 10px 22px rgba(0,150,136,0.35);
    transform: translateY(-3px);
}

</style>
@endpush

@section('content')

<div class="container py-5">

    <h3 class="fw-bold text-center mb-5 text-primary">
        🏟️ المركبات حسب النوع
    </h3>

    <div class="row g-4">

        @foreach($complexes as $complex)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">

            <div class="complex-mini-card">

                <div class="complex-img-circle">
                    @if($complex->image)
                        <img src="{{ asset($complex->image) }}">
                    @else
                        <img src="{{ asset('images/default-complex.jpg') }}">
                    @endif
                </div>

                <h5 class="complex-title">{{ $complex->nom }}</h5>

                <p class="complex-address">{{ $complex->adresse ?? 'العنوان غير متوفر' }}</p>

                <a href="{{ route('reservation.form', $complex->id) }}"
                   class="btn btn-register mx-auto">
                    تسجيل
                </a>

            </div>

        </div>
        @endforeach

    </div>

</div>

@endsection

