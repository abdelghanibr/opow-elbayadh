@extends('layouts.app')

@section('title', 'تأكيد الدفع - تذكرة المباراة')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right; max-width:950px">

    {{-- HEADER --}}
    <div class="ticket-pay-header mb-4">
        <div class="ticket-pay-header-icon">
            <i class="fa-solid fa-ticket"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-1">تأكيد حجز التذكرة</h4>
            <p class="mb-0 opacity-75">راجع التفاصيل ثم قم بالدفع الإلكتروني</p>
        </div>
    </div>

    <div class="row g-4">

        {{-- ================= MATCH INFO ================= --}}
        <div class="col-md-5">
            <div class="ticket-frame">
                <div class="ticket-frame-header">
                    <i class="fa-solid fa-futbol"></i>
                    تفاصيل المباراة
                </div>
                <div class="ticket-frame-body">

                    <div class="ticket-match-box text-center">
                        <div class="row align-items-center">

                            <div class="col-5">
                                <img src="{{ asset($match->homeTeam->logo) }}" class="ticket-team-logo">
                                <h6 class="fw-bold mt-2">{{ $match->homeTeam->name }}</h6>
                            </div>

                            <div class="col-2">
                                <div class="ticket-vs">VS</div>
                            </div>

                            <div class="col-5">
                                <img src="{{ asset($match->awayTeam->logo) }}" class="ticket-team-logo">
                                <h6 class="fw-bold mt-2">{{ $match->awayTeam->name }}</h6>
                            </div>

                        </div>

                        <div class="ticket-match-info mt-3">
                            <div><i class="fa-solid fa-location-dot"></i> {{ $match->complex->nom }}</div>
                            <div><i class="fa-solid fa-calendar"></i> {{ $match->match_date }}</div>
                            <div><i class="fa-solid fa-clock"></i> {{ $match->match_time }}</div>
                            @if($match->competition)
                                <div><i class="fa-solid fa-trophy"></i> {{ $match->competition }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="ticket-seat-badge mt-3">
                        <span class="ticket-seat-type">{{ $seatType->name }}</span>
                        <span class="ticket-seat-price">{{ number_format($seatType->price, 0, ',', ' ') }} دج</span>
                    </div>

                </div>
            </div>

            {{-- BUYER INFO --}}
            <div class="ticket-frame mt-4">
                <div class="ticket-frame-header">
                    <i class="fa-solid fa-user"></i>
                    معلومات المشتري
                </div>
                <div class="ticket-frame-body">
                    <div class="ticket-info-row">
                        <span class="ticket-info-label">الاسم الكامل</span>
                        <span class="ticket-info-value">{{ $ticketData['full_name'] }}</span>
                    </div>
                    <div class="ticket-info-row">
                        <span class="ticket-info-label">البريد الإلكتروني</span>
                        <span class="ticket-info-value">{{ $ticketData['email'] }}</span>
                    </div>
                    <div class="ticket-info-row">
                        <span class="ticket-info-label">رقم الهاتف</span>
                        <span class="ticket-info-value">{{ $ticketData['phone'] }}</span>
                    </div>
                    <div class="ticket-info-row">
                        <span class="ticket-info-label">رقم الهوية</span>
                        <span class="ticket-info-value">{{ $ticketData['identity_number'] }}</span>
                    </div>
                    <div class="ticket-info-row">
                        <span class="ticket-info-label">العمر</span>
                        <span class="ticket-info-value">{{ $ticketData['age'] }} سنة</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= PAYMENT ================= --}}
        <div class="col-md-7">
            <div class="ticket-frame">
                <div class="ticket-frame-header ticket-frame-header-green">
                    <i class="fa-solid fa-credit-card"></i>
                    إتمام الدفع
                </div>
                <div class="ticket-frame-body">

                    {{-- PRICE CARD --}}
                    <div class="ticket-price-card">
                        <div class="ticket-price-label">المبلغ الإجمالي الواجب دفعه</div>
                        <div class="ticket-price-amount">
                            {{ number_format($seatType->price, 0, ',', ' ') }}
                            <span class="ticket-price-currency">دج</span>
                        </div>
                        <div class="ticket-price-note">شامل جميع الرسوم</div>
                    </div>

                    {{-- ERRORS --}}
                    @if($errors->any() || session('error'))
                        <div style="background:#fef2f2; border:2px solid #fca5a5; border-radius:14px; padding:14px 18px; margin-bottom:18px;">
                            @foreach($errors->all() as $error)
                                <div style="color:#dc2626; font-weight:700; font-size:0.9rem; margin-bottom:4px;">
                                    <i class="fa-solid fa-circle-xmark"></i> {{ $error }}
                                </div>
                            @endforeach
                            @if(session('error'))
                                <div style="color:#dc2626; font-weight:700; font-size:0.9rem; margin-bottom:4px;">
                                    <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- CIB INFO --}}
                    <div class="ticket-cib-info">
                        <img src="{{ asset('images/cib.png') }}" alt="CIB" class="ticket-cib-logo">
                        <div>
                            <strong>تشغيل بيني لأنظمة الدفع الإلكتروني</strong><br>
                            <small>بطاقات CIB والذهبية (بريد الجزائر) مدعومة</small>
                        </div>
                    </div>

                    {{-- PAYMENT FORM --}}
                    <form action="{{ route('ticket.payment.initiate') }}" method="POST" id="ticketPaymentForm">
                        @csrf

                        <input type="hidden" name="card_type" id="cardType">

                        {{-- CARD CHOICE --}}
                        <div class="ticket-form-group">
                            <label class="ticket-form-label">
                                <i class="fa-solid fa-credit-card"></i>
                                اختر نوع البطاقة
                            </label>
                            <div class="ticket-card-choice">
                                <div class="ticket-satim-box selected" data-value="CIB" onclick="selectCard(this)">
                                    <img src="{{ asset('images/cib.png') }}" alt="CIB">
                                </div>
                            </div>
                        </div>

                        {{-- CAPTCHA --}}
                        @if (!app()->environment('local'))
                        <div class="ticket-form-group">
                            <label class="ticket-form-label">
                                <i class="fa-solid fa-shield-halved"></i>
                                التحقق الأمني
                            </label>
                            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                            @error('g-recaptcha-response')
                                <div class="ticket-form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        {{-- TERMS --}}
                        <div class="ticket-form-group">
                            <label class="ticket-checkbox-label">
                                <input type="checkbox" name="accept_terms" value="1" id="acceptTerms">
                                <span class="ticket-checkbox-custom"></span>
                                <span>
                                    أوافق على
                                    <a href="{{ route('terms.edahabia') }}" target="_blank" class="ticket-link">
                                        شروط الدفع الإلكتروني
                                    </a>
                                </span>
                            </label>
                            @error('accept_terms')
                                <div class="ticket-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SUBMIT --}}
                        <button type="submit" class="ticket-pay-btn" id="payBtn" disabled>
                            <i class="fa-solid fa-lock"></i>
                            الدفع الإلكتروني الآمن
                        </button>

                        <div class="ticket-pay-note">
                            <i class="fa-solid fa-circle-info"></i>
                            سيتم تحويلك إلى صفحة الدفع الآمنة
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('css')
<style>
body { font-family: "Cairo", sans-serif !important; }

/* HEADER */
.ticket-pay-header {
    display: flex;
    align-items: center;
    gap: 16px;
    background: linear-gradient(135deg, #082f57, #0b3d70);
    color: #fff;
    padding: 22px 28px;
    border-radius: 20px;
    box-shadow: 0 12px 30px rgba(8,47,87,0.2);
}

.ticket-pay-header-icon {
    width: 56px;
    height: 56px;
    background: rgba(255,255,255,0.15);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.ticket-pay-header h4 { margin: 0; }
.ticket-pay-header p { font-size: 0.9rem; }

/* FRAME */
.ticket-frame {
    background: #fff;
    border: 2px solid rgba(8,47,87,0.10);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 28px rgba(8,47,87,0.08);
}

.ticket-frame-header {
    background: linear-gradient(135deg, #082f57, #0b3d70);
    color: #fff;
    padding: 14px 20px;
    font-weight: 800;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ticket-frame-header-green {
    background: linear-gradient(135deg, #12a86b, #0f8f5d);
}

.ticket-frame-body {
    padding: 22px;
}

/* MATCH BOX */
.ticket-match-box {
    background: linear-gradient(135deg, #f8fafc, #eef2f7);
    border: 1px solid rgba(8,47,87,0.08);
    border-radius: 16px;
    padding: 20px 14px;
}

.ticket-team-logo {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e0e6f1;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.ticket-vs {
    font-size: 1.4rem;
    font-weight: 900;
    color: #dc2626;
    background: #fee2e2;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    border: 2px solid #fca5a5;
}

.ticket-match-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 0.88rem;
    color: #475569;
    font-weight: 700;
}

.ticket-match-info i {
    color: #082f57;
    width: 18px;
    text-align: center;
    margin-left: 6px;
}

/* SEAT BADGE */
.ticket-seat-badge {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f0fdf4;
    border: 2px solid #86efac;
    border-radius: 14px;
    padding: 12px 16px;
}

.ticket-seat-type {
    font-weight: 900;
    color: #16a34a;
    font-size: 1rem;
}

.ticket-seat-price {
    font-weight: 900;
    color: #082f57;
    font-size: 1.1rem;
    background: #fff;
    padding: 4px 14px;
    border-radius: 10px;
    border: 2px solid #082f57;
}

/* INFO ROW */
.ticket-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
}

.ticket-info-row:last-child { border-bottom: none; }

.ticket-info-label {
    color: #64748b;
    font-weight: 700;
    font-size: 0.9rem;
}

.ticket-info-value {
    color: #082f57;
    font-weight: 800;
    font-size: 0.95rem;
}

/* PRICE CARD */
.ticket-price-card {
    background: linear-gradient(135deg, #1a56db, #12a86b);
    color: #fff;
    border-radius: 18px;
    padding: 28px 20px;
    text-align: center;
    margin-bottom: 20px;
}

.ticket-price-label {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 8px;
}

.ticket-price-amount {
    font-size: 2.4rem;
    font-weight: 900;
    letter-spacing: 1px;
}

.ticket-price-currency {
    font-size: 1.1rem;
    opacity: 0.85;
}

.ticket-price-note {
    font-size: 0.8rem;
    opacity: 0.75;
    margin-top: 6px;
}

/* CIB INFO */
.ticket-cib-info {
    display: flex;
    align-items: center;
    gap: 14px;
    background: linear-gradient(135deg, #e0f2fe, #f0f9ff);
    border: 1px solid #bae6fd;
    border-radius: 14px;
    padding: 14px 16px;
    margin-bottom: 22px;
    font-size: 0.88rem;
    color: #0c4a6e;
    line-height: 1.6;
}

.ticket-cib-logo {
    height: 40px;
    flex-shrink: 0;
}

/* FORM */
.ticket-form-group {
    margin-bottom: 18px;
}

.ticket-form-label {
    display: block;
    font-weight: 800;
    color: #082f57;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.ticket-form-label i {
    margin-left: 6px;
    color: #12a86b;
}

.ticket-form-error {
    color: #dc2626;
    font-size: 0.85rem;
    font-weight: 700;
    margin-top: 6px;
}

/* CHECKBOX */
.ticket-checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 0.95rem;
    color: #334155;
    font-weight: 700;
    user-select: none;
}

.ticket-checkbox-label input[type="checkbox"] {
    display: none;
}

.ticket-checkbox-custom {
    width: 22px;
    height: 22px;
    border: 2px solid #93c5fd;
    border-radius: 6px;
    background: #fff;
    position: relative;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.ticket-checkbox-label input:checked + .ticket-checkbox-custom {
    background: linear-gradient(135deg, #12a86b, #0f8f5d);
    border-color: #12a86b;
}

.ticket-checkbox-label input:checked + .ticket-checkbox-custom::after {
    content: "✔";
    color: #fff;
    font-size: 13px;
    font-weight: bold;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -55%);
}

.ticket-link {
    color: #1a56db;
    font-weight: 800;
}

/* PAY BUTTON */
.ticket-pay-btn {
    width: 100%;
    padding: 16px;
    border: none;
    border-radius: 16px;
    background: linear-gradient(135deg, #12a86b, #0f8f5d);
    color: #fff;
    font-size: 1.1rem;
    font-weight: 900;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 10px 28px rgba(18,168,107,0.3);
    transition: all 0.3s ease;
}

.ticket-pay-btn:not(:disabled):hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 38px rgba(18,168,107,0.4);
}

.ticket-pay-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.ticket-pay-note {
    text-align: center;
    font-size: 0.82rem;
    color: #64748b;
    margin-top: 14px;
    font-weight: 700;
}

.ticket-pay-note i {
    margin-left: 4px;
    color: #1a56db;
}

/* RESPONSIVE */
@media (max-width: 767px) {
    .ticket-pay-header { padding: 18px 16px; }
    .ticket-frame-body { padding: 16px; }
    .ticket-team-logo { width: 56px; height: 56px; }
    .ticket-vs { width: 42px; height: 42px; font-size: 1.1rem; }
    .ticket-price-amount { font-size: 1.8rem; }
}

/* CARD CHOICE */
.ticket-card-choice {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.ticket-satim-box {
    flex: 0 0 auto;
    width: 140px;
    padding: 16px;
    border: 3px solid #e2e8f0;
    border-radius: 16px;
    background: #fff;
    cursor: pointer;
    text-align: center;
    transition: all 0.25s ease;
}

.ticket-satim-box:hover {
    border-color: #93c5fd;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

.ticket-satim-box.selected {
    border-color: #12a86b;
    background: #f0fdf4;
    box-shadow: 0 0 0 4px rgba(18,168,107,0.15);
}

.ticket-satim-box img {
    height: 40px;
    object-fit: contain;
}
</style>
@endpush

@if (!app()->environment('local'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('acceptTerms');
    const btn = document.getElementById('payBtn');
    if (checkbox && btn) {
        checkbox.addEventListener('change', function () {
            btn.disabled = !this.checked;
        });
    }

    // Auto-select CIB on load
    var firstCard = document.querySelector('.ticket-satim-box');
    if (firstCard) {
        document.getElementById('cardType').value = firstCard.getAttribute('data-value');
    }
});

function selectCard(el) {
    document.querySelectorAll('.ticket-satim-box').forEach(function(b) { b.classList.remove('selected'); });
    el.classList.add('selected');
    document.getElementById('cardType').value = el.getAttribute('data-value');
}
</script>
