@extends('layouts.app')

@section('title', 'إتمام الدفع')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right; max-width:900px">

    <h4 class="mb-4">💳 إتمام الدفع</h4>

    <div class="row g-4">

        {{-- ================= USER INFO ================= --}}
        <div class="col-md-5">
            <div class="card shadow-sm p-3">
                <h6 class="fw-bold mb-3">👤 معلومات المستخدم</h6>

                <p><strong>الاسم:</strong> {{ auth()->user()->name ?? 'غير متوفر' }}</p>
                <p><strong>البريد الإلكتروني:</strong> {{ auth()->user()->email ?? 'غير متوفر' }}</p>

                <p>
                    <strong>نوع الحساب:</strong>
                    @switch(auth()->user()->type)
                        @case('person') فرد @break
                        @case('club') نادي @break
                        @case('company')
                        @case('entreprise') مؤسسة @break
                        @default غير معروف
                    @endswitch
                </p>

                @if(auth()->user()->phone)
                    <p><strong>الهاتف:</strong> {{ auth()->user()->phone }}</p>
                @endif
            </div>
            <div class="alert alert-info border-0 shadow-sm mt-4 d-flex align-items-center gap-3"
     style="background: linear-gradient(135deg,#e0f2fe,#f0f9ff);">

    <div>
        <img src="{{ asset('images/cib.png') }}"
             alt="CIB EDAHABIA"
             style="height:42px">
    </div>

    <div style="font-size:14px; line-height:1.6">
        <strong>تشغيل بيني لأنظمة الدفع الإلكتروني في الجزائر</strong><br>
        يُعلم أن التشغيل البيني بين بطاقات الدفع
        <strong>CIB</strong> و<strong>الذهبية (بريد الجزائر)</strong>
        أصبح رسميًا ساري المفعول في الجزائر منذ
        <strong>23 ديسمبر 2021</strong>.
    </div>

</div>
        </div>

        {{-- ================= PAYMENT INFO ================= --}}
        <div class="col-md-7">
            <div class="card p-4 shadow-sm">

                <h6 class="fw-bold mb-3">🧾 تفاصيل الحجز</h6>

                <p><strong>رقم الحجز:</strong> {{ $reservation->id }}</p>

                {{-- PRICE --}}
                <div class="card border-0 shadow-sm mb-3"
                     style="background: linear-gradient(135deg, #0d6efd, #20c997); color:#fff">
                    <div class="card-body text-center py-4">
                        <div class="mb-2" style="font-size:14px; opacity:.9">
                            💰 المبلغ الإجمالي الواجب دفعه
                        </div>

                       <div style="font-size:32px; font-weight:800; letter-spacing:1px">

    <span dir="ltr">
       {{ number_format(round($reservation->total_price), 0, ',', ' ') }}
    </span>
    دج

</div>


                        <div class="mt-2" style="font-size:13px; opacity:.85">
                            شامل كل الرسوم
                        </div>
                    </div>
                </div>

                {{-- STATUS --}}
                <p>
                    <strong>حالة الدفع:</strong>
                    @if($reservation->payment_status === 'paid')
                        <span class="badge bg-success">مدفوع</span>
                    @elseif($reservation->payment_status === 'pending')
                        <span class="badge bg-warning text-dark">قيد الانتظار</span>
                    @else
                        <span class="badge bg-danger">غير مدفوع</span>
                    @endif
                </p>

             
                {{-- ================= INTEROPERABILITÉ ================= --}}



                {{-- PAYMENT FORM --}}
<form action="{{ route('payment.initiate') }}" method="POST" id="paymentForm">
    @csrf

    <input type="hidden" name="amount"
           value="{{ (int) $reservation->total_price }}">

    <input type="hidden" name="reservation_id"
           value="{{ $reservation->id }}">

    <input type="hidden" name="card_type" id="cardType">

    {{-- ===== CARD CHOICE ===== --}}
    <div class="satim-choice mb-3">

      <div class="satim-choice mb-3">

    <div class="satim-box"
         data-value="CIB">
        <img src="{{ asset('images/cib.png') }}" alt="CIB">
    </div>

   

</div>



    </div>

    {{-- CAPTCHA --}}
    @if (!app()->environment('local'))
    <label class="mt-2">التحقق</label>
    <div class="captcha-wrapper mb-2">
        <div class="g-recaptcha"
             data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}">
        </div>
    </div>

    @error('g-recaptcha-response')
        <div class="text-danger small mb-2">
            يرجى إتمام التحقق الأمني
        </div>
    @enderror
    @endif

    {{-- TERMS --}}
    <div class="form-check form-check-rtl mb-2">
        <input class="form-check-input"
               type="checkbox"
               name="accept_terms"
               value="1"
               id="acceptTerms">

      <label class="form-check-label" for="acceptTerms">

أوافق على 
<a href="{{ route('terms.edahabia') }}" target="_blank">
شروط الدفع الإلكتروني
</a>

</label>
    </div>

    @error('accept_terms')
        <div class="text-danger small mb-2">
            يجب الموافقة على شروط الدفع
        </div>
    @enderror

    {{-- BUTTON --}}
    <button type="submit"
            class="btn btn-success btn-lg w-100 shadow-sm pay-satim-btn"
            id="payBtn"
            disabled>

        <span class="d-flex align-items-center justify-content-center gap-2">
            <span>🔐 الدفع الإلكتروني</span>
        </span>

    </button>

</form>

<div class="text-center mt-3">

    <div style="font-size:13.5px; color:#14532d; margin-bottom:6px;">
        في حال وجود مشكلة في بطاقتك CIB أو الذهبية<br>
        يرجى الاتصال بمركز الدعم SATIM
    </div>

    <img src="{{ asset('images/app.png') }}"
         alt="SATIM 3020"
         style="height:48px">

</div>

            </div>
        </div>

    </div>
</div>
@endsection
@push('css')
<style>
.form-check-rtl {
    display: flex;
    flex-direction: row-reverse;
    align-items: center;
    gap: 10px;
    direction: rtl;
    font-size: 15px;
    color: #1f2937;
}

/* إخفاء الشكل الافتراضي */
.form-check-rtl .form-check-input {
    appearance: none;
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solid #93c5fd;
    border-radius: 6px;
    background: #fff;
    cursor: pointer;
    position: relative;
    transition: all .2s ease;
}

/* Hover */
.form-check-rtl .form-check-input:hover {
    border-color: #38bdf8;
    box-shadow: 0 0 0 3px rgba(56,189,248,0.15);
}

/* Checked */
.form-check-rtl .form-check-input:checked {
    background: linear-gradient(135deg, #38bdf8, #0ea5e9);
    border-color: #0ea5e9;
}

/* ✔ */
.form-check-rtl .form-check-input:checked::after {
    content: "✔";
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -55%);
}

/* Label */
.form-check-rtl .form-check-label {
    cursor: pointer;
    user-select: none;
    line-height: 1.6;
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

    if (!checkbox || !btn) return;

    checkbox.addEventListener('change', function () {
        btn.disabled = !this.checked;
    });

});
</script>


