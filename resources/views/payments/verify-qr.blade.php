@extends('layouts.app')

@section('title', 'تحقق من التذكرة')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right; max-width:700px">

    <div class="verify-card">
        <div class="verify-header">
            <i class="fa-solid fa-shield-halved"></i>
            <h4>أداة التحقق من التذكرة</h4>
            <p>امسح الرمز أو أدخل البيانات للتحقق من صلاحية التذكرة</p>
        </div>

        {{-- RESULT --}}
        @if(isset($result))
            @if($result === 'valid')
                <div class="verify-result valid">
                    <div class="result-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <h5>{{ $message }}</h5>
                    @if(isset($ticket))
                        <div class="result-details">
                            <div class="result-row"><span>رقم التذكرة</span><strong>#{{ $ticket->id }}</strong></div>
                            <div class="result-row"><span>الاسم</span><strong>{{ $ticket->buyer_name }}</strong></div>
                            <div class="result-row"><span>المباراة</span><strong>{{ $ticket->match->homeTeam->name }} vs {{ $ticket->match->awayTeam->name }}</strong></div>
                            <div class="result-row"><span>نوع المقعد</span><strong>{{ $ticket->seatType->name }}</strong></div>
                            <div class="result-row"><span>السعر</span><strong>{{ number_format($ticket->seatType->price, 0, ',', '.') }} د.ج</strong></div>
                            <div class="result-row"><span>التاريخ</span><strong>{{ $ticket->match->match_date }} {{ $ticket->match->match_time }}</strong></div>
                            <div class="result-row"><span>الحالة</span><strong style="color:#12a86b">مدفوعة</strong></div>
                        </div>
                    @endif
                </div>

            @elseif($result === 'tampered')
                <div class="verify-result tampered">
                    <div class="result-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <h5>{{ $message }}</h5>
                    @if(isset($ticket) && isset($checks))
                        <div class="result-details">
                            <div class="result-row"><span>رقم التذكرة</span><strong>#{{ $ticket->id }}</strong></div>
                            <div class="check-item {{ $checks['buyer'] ? 'ok' : 'fail' }}">
                                <i class="fa-solid fa-{{ $checks['buyer'] ? 'check' : 'xmark' }}"></i> الاسم
                            </div>
                            <div class="check-item {{ $checks['seat'] ? 'ok' : 'fail' }}">
                                <i class="fa-solid fa-{{ $checks['seat'] ? 'check' : 'xmark' }}"></i> نوع المقعد
                            </div>
                            <div class="check-item {{ $checks['amount'] ? 'ok' : 'fail' }}">
                                <i class="fa-solid fa-{{ $checks['amount'] ? 'check' : 'xmark' }}"></i> السعر
                            </div>
                            <div class="check-item {{ $checks['date'] ? 'ok' : 'fail' }}">
                                <i class="fa-solid fa-{{ $checks['date'] ? 'check' : 'xmark' }}"></i> التاريخ
                            </div>
                            <div class="check-item {{ $checks['time'] ? 'ok' : 'fail' }}">
                                <i class="fa-solid fa-{{ $checks['time'] ? 'check' : 'xmark' }}"></i> الوقت
                            </div>
                        </div>
                    @endif
                </div>

            @else
                <div class="verify-result invalid">
                    <div class="result-icon"><i class="fa-solid fa-circle-xmark"></i></div>
                    <h5>{{ $message }}</h5>
                    @if(isset($qrData))
                        <div class="result-details">
                            <div class="result-row"><span>الرقم</span><strong>#{{ $qrData['id'] ?? '—' }}</strong></div>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        {{-- SCANNER --}}
        <div class="verify-section">
            <h6><i class="fa-solid fa-camera"></i> مسح بالكاميرا</h6>
            <div id="reader" style="width:100%; border-radius:12px; overflow:hidden;"></div>
            <div id="scan-result" class="scan-result-box"></div>
        </div>

        <div class="verify-divider">
            <span>أو</span>
        </div>

        {{-- MANUAL INPUT --}}
        <div class="verify-section">
            <h6><i class="fa-solid fa-keyboard"></i> إدخال يدوي</h6>
            <form action="{{ route('ticket.check-qr') }}" method="POST" id="manualForm">
                @csrf
                <textarea name="qr_data" id="qrDataInput" rows="4" placeholder="الصق محتوى الرمز هنا..." class="verify-textarea"></textarea>
                <button type="submit" class="verify-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    تحقق
                </button>
            </form>
        </div>

        <div class="verify-section mt-3">
            <a href="{{ route('matches.public') }}" class="verify-btn outline">
                <i class="fa-solid fa-futbol"></i>
                العودة للمباريات
            </a>
        </div>
    </div>

</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const html5QrCode = new Html5Qrcode("reader");

    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        },
        (decodedText) => {
            html5QrCode.stop();
            document.getElementById('qrDataInput').value = decodedText;
            document.getElementById('manualForm').submit();
        },
        (errorMessage) => {}
    ).catch((err) => {
        document.getElementById('reader').innerHTML =
            '<div style="text-align:center;padding:20px;color:#94a3b8;font-size:0.85rem;">' +
            '<i class="fa-solid fa-video-slash" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>' +
            'الكاميرا غير متوفرة — استخدم الإدخال اليدوي</div>';
    });
</script>
@endsection

@push('css')
<style>
body { font-family: "Cairo", sans-serif !important; }

.verify-card {
    background: #fff;
    border: 2px solid rgba(8,47,87,0.10);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 12px 36px rgba(8,47,87,0.10);
}

.verify-header {
    text-align: center;
    margin-bottom: 24px;
}

.verify-header i {
    font-size: 2.5rem;
    color: #082f57;
    margin-bottom: 8px;
}

.verify-header h4 {
    font-weight: 900;
    color: #082f57;
    margin-bottom: 4px;
}

.verify-header p {
    color: #64748b;
    font-size: 0.85rem;
}

.verify-section {
    margin-bottom: 16px;
}

.verify-section h6 {
    font-weight: 800;
    color: #082f57;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.verify-textarea {
    width: 100%;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px;
    font-family: 'Cairo', sans-serif;
    font-size: 0.85rem;
    resize: vertical;
    transition: border-color 0.2s;
    direction: ltr;
    text-align: left;
}

.verify-textarea:focus {
    outline: none;
    border-color: #082f57;
}

.verify-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 14px;
    font-family: 'Cairo', sans-serif;
    font-weight: 800;
    font-size: 0.9rem;
    cursor: pointer;
    border: none;
    background: linear-gradient(135deg, #082f57, #0b3d70);
    color: #fff;
    transition: all 0.2s ease;
    text-decoration: none;
    margin-top: 10px;
}

.verify-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(8,47,87,0.3);
    color: #fff;
}

.verify-btn.outline {
    background: #fff;
    color: #082f57;
    border: 2px solid #082f57;
}

.verify-btn.outline:hover {
    background: #082f57;
    color: #fff;
}

.verify-divider {
    text-align: center;
    margin: 16px 0;
    position: relative;
}

.verify-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    border-top: 2px solid #e2e8f0;
}

.verify-divider span {
    background: #fff;
    padding: 0 16px;
    position: relative;
    color: #94a3b8;
    font-weight: 700;
    font-size: 0.85rem;
}

.verify-result {
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
    border: 2px solid;
}

.verify-result.valid {
    background: #f0fdf4;
    border-color: #86efac;
}

.verify-result.tampered {
    background: #fffbeb;
    border-color: #fde047;
}

.verify-result.invalid {
    background: #fef2f2;
    border-color: #fca5a5;
}

.result-icon {
    font-size: 3rem;
    margin-bottom: 8px;
}

.verify-result.valid .result-icon { color: #12a86b; }
.verify-result.tampered .result-icon { color: #eab308; }
.verify-result.invalid .result-icon { color: #dc2626; }

.verify-result h5 {
    font-weight: 800;
    margin-bottom: 12px;
}

.verify-result.valid h5 { color: #12a86b; }
.verify-result.tampered h5 { color: #b45309; }
.verify-result.invalid h5 { color: #dc2626; }

.result-details {
    background: #fff;
    border-radius: 12px;
    padding: 12px;
    text-align: right;
}

.result-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
}

.result-row:last-child { border-bottom: none; }
.result-row span { color: #64748b; font-weight: 700; }
.result-row strong { color: #082f57; font-weight: 800; }

.check-item {
    padding: 6px 10px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.8rem;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.check-item.ok { background: #f0fdf4; color: #12a86b; }
.check-item.fail { background: #fef2f2; color: #dc2626; }

.scan-result-box {
    margin-top: 10px;
    text-align: center;
    font-weight: 700;
    font-size: 0.85rem;
}
</style>
@endpush
