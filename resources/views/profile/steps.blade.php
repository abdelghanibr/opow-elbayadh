@extends('layouts.app')

@section('content')
<style>
/* ======== Modern Stepper 2025 ======== */
.profile-progress {
    width: 100%;
    height: 7px;
    background: #e5e7eb;
    border-radius: 50px;
    margin-bottom: 30px;
    position: relative;
}
.profile-progress-bar {
    background: linear-gradient(90deg, #198754, #23d36b);
    height: 7px;
    width: {{ ($step / 4) * 100 }}%;
    border-radius: 50px;
    transition: .4s ease;
}

.stepper-wrapper {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 10px;
}
.stepper-item {
    text-align: center;
    flex-shrink: 0;
    width: 110px;
}
.step-counter {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    margin: auto;
    font-size: 20px;
    font-weight: bold;
    background: #cfd1d4;
    color: #444;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: .3s;
}
.step-name {
    font-size: 13px;
    font-weight: 600;
    margin-top: 8px;
    white-space: nowrap;
    color: #6c757d;
}

.stepper-item.active .step-counter {
    background: #198754;
    color: #fff;
    transform: scale(1.07);
}
.stepper-item.active .step-name {
    color: #198754;
}
.stepper-item.completed .step-counter {
    background: #28a745;
    color: #fff;
}
.stepper-item.completed .step-name {
    color: #28a745;
}

/* 🔹 لإخفاء Scroll على الحاسوب */
.stepper-wrapper::-webkit-scrollbar {
    height: 5px;
}
.stepper-wrapper::-webkit-scrollbar-thumb {
    background: #198754;
    border-radius: 4px;
}

.box-area {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 15px rgba(0,0,0,.07);
}
</style>


<div class="container py-4" style="direction: rtl; text-align:right;">

    <!-- Progress -->
    <div class="profile-progress">
        <div class="profile-progress-bar"></div>
    </div>

    <!-- Stepper -->
    <div class="stepper-wrapper">
        @for ($i = 1; $i <= 4; $i++)
            <div class="stepper-item 
                {{ $step == $i ? 'active' : '' }}
                {{ $step > $i ? 'completed' : '' }}
            ">
                <div class="step-counter">
                    @if($i == 1) <i class="fa-solid fa-user"></i>
                    @elseif($i == 2) <i class="fa-solid fa-user-shield"></i>
                    @elseif($i == 3) <i class="fa-solid fa-info-circle"></i>
                    @elseif($i == 4) <i class="fa-solid fa-file-medical"></i>
                    @endif
                </div>
                <div class="step-name">
                    @if($i == 1) المعلومات الأساسية
                    @elseif($i == 2) معلومات الولي
                    @elseif($i == 3) معلومات إضافية
                    @elseif($i == 4) الوثائق المطلوبة
                    @endif
                </div>
            </div>
        @endfor
    </div>

    <!-- Errors -->
    @if ($errors->any())
        <div class="alert alert-danger text-right">
            ⚠ يرجى ملء كل الحقول المطلوبة بشكل صحيح.
        </div>
    @endif

    <!-- Step Content -->
    <div class="box-area">
        @include('profile.steps_parts.step' . $step)
    </div>

</div>

@endsection
