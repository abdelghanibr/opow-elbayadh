@extends('layouts.app')

@section('title', 'حجز تذكرة المباراة')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right; max-width:950px">

    <div class="ticket-header mb-4">
        <div class="ticket-header-icon"><i class="fa-solid fa-ticket"></i></div>
        <div>
            <h4 class="fw-bold mb-1">حجز تذكرة المباراة</h4>
            <p class="mb-0 opacity-75">اختر نوع المقعد ثم أكمل المعلومات</p>
        </div>
    </div>

    <div class="ticket-steps mb-4">
        <div class="ticket-step active" id="stepIndicator1">
            <div class="ticket-step-num">1</div>
            <span>اختيار المقعد</span>
        </div>
        <div class="ticket-step-line"></div>
        <div class="ticket-step" id="stepIndicator2">
            <div class="ticket-step-num">2</div>
            <span>معلومات المشتري</span>
        </div>
    </div>

    {{-- STEP 1 --}}
    <div id="step1">
        <div class="ticket-frame">
            <div class="ticket-frame-header"><i class="fa-solid fa-futbol"></i> تفاصيل المباراة</div>
            <div class="ticket-frame-body">
                <div class="ticket-match-display">
                    <div class="row align-items-center">
                        <div class="col-4 text-center">
                            <img src="{{ asset($match->homeTeam->logo) }}" class="ticket-team-img">
                            <h6 class="fw-bold mt-2">{{ $match->homeTeam->name }}</h6>
                        </div>
                        <div class="col-4 text-center">
                            <div class="ticket-vs-badge">VS</div>
                            <div class="ticket-match-meta">
                                <div><i class="fa-solid fa-location-dot"></i> {{ $match->complex->nom }}</div>
                                <div><i class="fa-solid fa-calendar"></i> {{ $match->match_date }}</div>
                                <div><i class="fa-solid fa-clock"></i> {{ $match->match_time }}</div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <img src="{{ asset($match->awayTeam->logo) }}" class="ticket-team-img">
                            <h6 class="fw-bold mt-2">{{ $match->awayTeam->name }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ticket-frame mt-4">
            <div class="ticket-frame-header"><i class="fa-solid fa-chair"></i> اختيار نوع المقعد</div>
            <div class="ticket-frame-body">
                <div class="ticket-seat-grid">
                    @foreach($complexSeats as $s)
                        @php
                            $cls = match($s->seatType->name) { 'VIP' => 'vip', 'Premium' => 'premium', 'Basic' => 'basic', default => 'regular' };
                            $disabled = $s->remaining <= 0;
                        @endphp
                        <div class="seat-card {{ $cls }} {{ $disabled ? 'disabled-card' : '' }}"
                             data-id="{{ $s->seat_type_id }}"
                             data-name="{{ $s->seatType->name }}"
                             data-price="{{ $s->seatType->price }}"
                             onclick="selectSeat(this)">
                            <div class="seat-check"><i class="fa-solid fa-check"></i></div>
                            <div class="seat-icon-box">
                                @if($cls == 'vip') <i class="fa-solid fa-crown"></i>
                                @elseif($cls == 'premium') <i class="fa-solid fa-star"></i>
                                @elseif($cls == 'basic') <i class="fa-solid fa-chair"></i>
                                @else <i class="fa-solid fa-circle-dot"></i> @endif
                            </div>
                            <div class="seat-name">{{ $s->seatType->name }}</div>
                            <div class="seat-price">{{ number_format($s->seatType->price, 0, ',', ' ') }} دج</div>
                            <div class="seat-status {{ $disabled ? 'status-no' : 'status-ok' }}">
                                @if($disabled) <i class="fa-solid fa-xmark-circle"></i> نفدت
                                @else <i class="fa-solid fa-check-circle"></i> {{ $s->remaining }} متاح @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <button type="button" id="nextBtn" onclick="goToStep2()" class="next-btn" disabled>
                        <i class="fa-solid fa-arrow-left"></i> المتابعة إلى المعلومات
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 2 --}}
    <div id="step2" style="display:none">
        <div class="ticket-frame">
            <div class="ticket-frame-header green-header"><i class="fa-solid fa-user-pen"></i> معلومات المشتري</div>
            <div class="ticket-frame-body">
                <div class="selected-info">
                    <i class="fa-solid fa-chair"></i>
                    <span id="sumName">-</span>
                    <span class="sum-price" id="sumPrice">0 دج</span>
                </div>

                <form id="userForm" action="{{ route('ticket.confirm.pay') }}" method="POST">
                    @csrf
                    <input type="hidden" name="match_id" value="{{ $match->id }}">
                    <input type="hidden" id="hSeat" name="seat_type_id">

                    <div class="form-grid">
                        <div class="field-box">
                            <label><i class="fa-solid fa-user"></i> الاسم الكامل</label>
                            <input type="text" name="full_name" required placeholder="أدخل الاسم الكامل">
                        </div>
                        <div class="field-box">
                            <label><i class="fa-solid fa-envelope"></i> البريد الإلكتروني</label>
                            <input type="email" name="email" required placeholder="example@email.com">
                        </div>
                        <div class="field-box">
                            <label><i class="fa-solid fa-phone"></i> رقم الهاتف</label>
                            <input type="text" name="phone" required placeholder="0XXX XX XX XX">
                        </div>
                        <div class="field-box">
                            <label><i class="fa-solid fa-id-card"></i> رقم الهوية</label>
                            <input type="text" name="identity_number" required placeholder="رقم الوثيقة">
                        </div>
                        <div class="field-box">
                            <label><i class="fa-solid fa-cake-candles"></i> العمر</label>
                            <input type="number" name="age" required min="5" max="120" placeholder="العمر">
                        </div>
                    </div>

                    <div class="form-actions mt-4">
                        <button type="button" onclick="goBack()" class="back-btn"><i class="fa-solid fa-arrow-right"></i> العودة</button>
                        <button type="submit" class="submit-btn"><i class="fa-solid fa-credit-card"></i> المتابعة إلى الدفع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<style>
body { font-family: "Cairo", sans-serif !important; }

.ticket-header {
    display: flex; align-items: center; gap: 16px;
    background: linear-gradient(135deg, #082f57, #0b3d70);
    color: #fff; padding: 22px 28px; border-radius: 20px;
    box-shadow: 0 12px 30px rgba(8,47,87,0.2);
}
.ticket-header-icon {
    width: 56px; height: 56px; background: rgba(255,255,255,0.15);
    border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
}

.ticket-steps { display: flex; align-items: center; justify-content: center; gap: 0; }
.ticket-step {
    display: flex; align-items: center; gap: 8px; padding: 10px 20px;
    border-radius: 999px; background: #f1f5f9; color: #94a3b8; font-weight: 800; font-size: 0.9rem;
}
.ticket-step.active { background: linear-gradient(135deg, #082f57, #0b3d70); color: #fff; box-shadow: 0 6px 18px rgba(8,47,87,0.2); }
.ticket-step-num { width: 28px; height: 28px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 900; }
.ticket-step-line { width: 50px; height: 3px; background: #e2e8f0; border-radius: 999px; }

.ticket-frame { background: #fff; border: 2px solid rgba(8,47,87,0.10); border-radius: 20px; overflow: hidden; box-shadow: 0 8px 28px rgba(8,47,87,0.08); }
.ticket-frame-header { background: linear-gradient(135deg, #082f57, #0b3d70); color: #fff; padding: 14px 20px; font-weight: 800; font-size: 1rem; display: flex; align-items: center; gap: 10px; }
.green-header { background: linear-gradient(135deg, #12a86b, #0f8f5d); }
.ticket-frame-body { padding: 22px; }

.ticket-match-display { background: linear-gradient(135deg, #f8fafc, #eef2f7); border: 1px solid rgba(8,47,87,0.08); border-radius: 16px; padding: 24px 14px; }
.ticket-team-img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #e0e6f1; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.ticket-vs-badge { font-size: 1.4rem; font-weight: 900; color: #dc2626; background: #fee2e2; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; border: 2px solid #fca5a5; }
.ticket-match-meta { display: flex; flex-direction: column; gap: 4px; font-size: 0.85rem; color: #475569; font-weight: 700; }
.ticket-match-meta i { color: #082f57; width: 16px; margin-left: 4px; }

.ticket-seat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; }

/* ===== SEAT CARD ===== */
.seat-card {
    position: relative; padding: 24px 16px; border-radius: 18px; text-align: center;
    border: 3px solid #e2e8f0; background: #fff; cursor: pointer;
    transition: all 0.3s ease;
}
.seat-card:hover:not(.disabled-card) { transform: translateY(-6px); box-shadow: 0 12px 28px rgba(0,0,0,0.12); }

.seat-card.vip { border-color: #fde047; background: linear-gradient(180deg, #fefce8, #fff); }
.seat-card.premium { border-color: #c4b5fd; background: linear-gradient(180deg, #f5f3ff, #fff); }
.seat-card.basic { border-color: #86efac; background: linear-gradient(180deg, #f0fdf4, #fff); }

.seat-card.disabled-card { opacity: 0.45; cursor: not-allowed; pointer-events: none; }

/* SELECTED STATE */
.seat-card.active {
    border-color: #082f57 !important;
    background: #eff6ff !important;
    box-shadow: 0 0 0 4px rgba(8,47,87,0.2), 0 8px 30px rgba(8,47,87,0.18) !important;
    transform: scale(1.06) !important;
}

.seat-check {
    position: absolute; top: 8px; left: 8px;
    width: 28px; height: 28px; border-radius: 50%;
    background: #12a86b; color: #fff; font-size: 14px;
    display: none; align-items: center; justify-content: center;
    box-shadow: 0 4px 10px rgba(18,168,107,0.4);
}
.seat-card.active .seat-check { display: flex; }

.seat-icon-box { font-size: 2.2rem; margin-bottom: 10px; }
.vip .seat-icon-box { color: #eab308; }
.premium .seat-icon-box { color: #8b5cf6; }
.basic .seat-icon-box { color: #22c55e; }

.seat-name { font-weight: 900; font-size: 1.05rem; color: #082f57; margin-bottom: 6px; }
.seat-price { font-weight: 800; font-size: 0.95rem; background: #fff; display: inline-block; padding: 4px 14px; border-radius: 10px; border: 2px solid #082f57; color: #082f57; margin-bottom: 8px; }
.seat-status { font-size: 0.82rem; font-weight: 700; }
.status-ok { color: #16a34a; }
.status-no { color: #dc2626; }

/* ===== NEXT BUTTON ===== */
.next-btn {
    padding: 16px 44px; border: none; border-radius: 14px;
    background: #94a3b8; color: #fff; font-size: 1.1rem; font-weight: 900;
    cursor: not-allowed; display: inline-flex; align-items: center; gap: 10px;
    transition: all 0.3s ease;
}
.next-btn.ready {
    background: linear-gradient(135deg, #12a86b, #0f8f5d);
    cursor: pointer;
    box-shadow: 0 8px 28px rgba(18,168,107,0.35);
    animation: pulse 2s ease-in-out infinite;
}
.next-btn.ready:hover { transform: translateY(-3px) scale(1.03); box-shadow: 0 14px 36px rgba(18,168,107,0.45); }

@keyframes pulse {
    0%, 100% { box-shadow: 0 8px 28px rgba(18,168,107,0.35); }
    50% { box-shadow: 0 8px 40px rgba(18,168,107,0.55), 0 0 0 6px rgba(18,168,107,0.1); }
}

/* ===== STEP 2 ===== */
.selected-info {
    display: flex; align-items: center; gap: 12px;
    background: #f0fdf4; border: 2px solid #86efac; border-radius: 14px;
    padding: 14px 18px; margin-bottom: 22px; font-weight: 800; color: #16a34a;
}
.sum-price { margin-right: auto; background: #fff; padding: 4px 14px; border-radius: 10px; border: 2px solid #082f57; color: #082f57; font-weight: 900; }

.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
.field-box label { display: block; font-weight: 800; color: #082f57; margin-bottom: 6px; font-size: 0.9rem; }
.field-box label i { margin-left: 6px; color: #12a86b; }
.field-box input {
    width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px;
    font-size: 0.95rem; font-weight: 700; font-family: "Cairo", sans-serif;
    background: #f8fafc; transition: all 0.2s ease;
}
.field-box input:focus { outline: none; border-color: #082f57; box-shadow: 0 0 0 3px rgba(8,47,87,0.1); background: #fff; }

.form-actions { display: flex; gap: 12px; justify-content: space-between; }
.back-btn {
    padding: 14px 28px; border: 2px solid #e2e8f0; border-radius: 14px;
    background: #fff; color: #475569; font-size: 1rem; font-weight: 800;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
}
.back-btn:hover { border-color: #082f57; color: #082f57; }
.submit-btn {
    padding: 14px 32px; border: none; border-radius: 14px;
    background: linear-gradient(135deg, #12a86b, #0f8f5d);
    color: #fff; font-size: 1rem; font-weight: 900; cursor: pointer;
    display: inline-flex; align-items: center; gap: 10px;
    box-shadow: 0 8px 24px rgba(18,168,107,0.25);
}
.submit-btn:hover { transform: translateY(-3px); box-shadow: 0 14px 32px rgba(18,168,107,0.35); }

@media (max-width: 767px) {
    .ticket-header { padding: 18px 16px; }
    .ticket-frame-body { padding: 16px; }
    .ticket-team-img { width: 60px; height: 60px; }
    .ticket-vs-badge { width: 44px; height: 44px; font-size: 1.1rem; }
    .form-grid { grid-template-columns: 1fr; }
    .form-actions { flex-direction: column; }
    .ticket-steps { flex-direction: column; gap: 8px; }
    .ticket-step-line { width: 3px; height: 20px; }
}
</style>

<script>
var _sid = null, _sname = null, _sprice = null;

function selectSeat(el) {
    if (el.classList.contains('disabled-card')) return;
    var cards = document.querySelectorAll('.seat-card');
    for (var i = 0; i < cards.length; i++) cards[i].classList.remove('active');
    el.classList.add('active');
    _sid = el.getAttribute('data-id');
    _sname = el.getAttribute('data-name');
    _sprice = el.getAttribute('data-price');
    var btn = document.getElementById('nextBtn');
    btn.disabled = false;
    btn.classList.add('ready');
    btn.innerHTML = '<i class="fa-solid fa-arrow-left"></i> المتابعة — ' + _sname;
}

function goToStep2() {
    if (!_sid) { alert('الرجاء اختيار نوع المقعد'); return; }
    document.getElementById('hSeat').value = _sid;
    document.getElementById('sumName').textContent = _sname;
    document.getElementById('sumPrice').textContent = parseFloat(_sprice).toLocaleString('ar-DZ') + ' دج';
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
    document.getElementById('stepIndicator1').classList.remove('active');
    document.getElementById('stepIndicator2').classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goBack() {
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
    document.getElementById('stepIndicator1').classList.add('active');
    document.getElementById('stepIndicator2').classList.remove('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endsection
