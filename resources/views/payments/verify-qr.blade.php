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
                            <div class="result-row"><span>وقت المسح</span><strong>{{ optional($ticket->scanned_at)->format('Y/m/d H:i') }}</strong></div>
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

            @elseif($result === 'already_scanned')
                <div class="verify-result already-scanned">
                    <div class="result-icon"><i class="fa-solid fa-ban"></i></div>
                    <h5>{{ $message }}</h5>
                    @if(isset($ticket))
                        <div class="result-details">
                            <div class="result-row"><span>رقم التذكرة</span><strong>#{{ $ticket->id }}</strong></div>
                            <div class="result-row"><span>الاسم</span><strong>{{ $ticket->buyer_name }}</strong></div>
                            <div class="result-row"><span>المباراة</span><strong>{{ $ticket->match->homeTeam->name }} vs {{ $ticket->match->awayTeam->name }}</strong></div>
                            <div class="result-row"><span>نوع المقعد</span><strong>{{ $ticket->seatType->name }}</strong></div>
                            <div class="result-row"><span>وقت أول مسح</span><strong>{{ optional($ticket->scanned_at)->format('Y/m/d H:i') ?? '—' }}</strong></div>
                            <div class="result-row"><span>بواسطة</span><strong>{{ $ticket->scanner->name ?? 'غير معروف' }}</strong></div>
                        </div>

                        @if(auth()->user()?->type === 'admin')
                            <form action="{{ route('ticket.reset-scan') }}" method="POST" class="mt-3">
                                @csrf
                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                <button type="submit" class="verify-btn outline">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    إلغاء الوسم وإعادة المسح
                                </button>
                            </form>
                        @endif
                    @endif
                </div>

            @elseif($result === 'reset')
                <div class="verify-result reset">
                    <div class="result-icon"><i class="fa-solid fa-rotate-left"></i></div>
                    <h5>{{ $message }}</h5>
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
            @if(isset($result))
                <a href="{{ route('tickets.verify-qr') }}" class="verify-btn outline mt-3" style="text-decoration:none">
                    <i class="fa-solid fa-rotate-right"></i> مسح تذكرة أخرى
                </a>
            @endif
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

        <div class="verify-section mt-3 pwa-install-box" id="pwaInstallBox" style="display:none">
            <div class="pwa-install-content">
                <img src="/pwa/icons/icon-192.png" alt="تحقق QR" class="pwa-install-icon">
                <div class="pwa-install-text">
                    <strong>تثبيت تطبيق «تحقق QR»</strong>
                    <span>ثبّت التطبيق للوصول السريع إلى أداة التحقق</span>
                </div>
                <button type="button" id="pwaInstallBtn" class="verify-btn">
                    <i class="fa-solid fa-download"></i> تثبيت
                </button>
            </div>
        </div>
    </div>

</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    (function () {
        const HAS_RESULT = @if(isset($result)) true @else false @endif;
        if (HAS_RESULT) return;

        let submitted = false;

        function submitOnce(decodedText) {
            if (submitted) return;
            submitted = true;

            const box = document.getElementById('scan-result');
            if (box) box.innerHTML = '<div style="text-align:center;padding:12px;color:#94a3b8;font-size:0.85rem;">جارٍ التحقق…</div>';

            const stop = html5QrCode.stop();
            if (stop && typeof stop.catch === 'function') stop.catch(function () {});

            document.getElementById('qrDataInput').value = decodedText;
            document.getElementById('manualForm').submit();
        }

        const html5QrCode = new Html5Qrcode("reader");

        html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            },
            (decodedText) => submitOnce(decodedText),
            (errorMessage) => {}
        ).catch((err) => {
        document.getElementById('reader').innerHTML =
            '<div style="text-align:center;padding:20px;color:#94a3b8;font-size:0.85rem;">' +
            '<i class="fa-solid fa-video-slash" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>' +
            'الكاميرا غير متوفرة — استخدم الإدخال اليدوي</div>';
    });
    })();

    /* ======== PWA : Service Worker ======== */
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/pwa/sw.js').catch(function (err) {
                console.warn('SW registration failed', err);
            });
        });
    }

    /* ======== PWA : bannière d'installation ======== */
    let deferredPrompt = null;
    const installBox = document.getElementById('pwaInstallBox');
    const installBtn = document.getElementById('pwaInstallBtn');

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        if (installBox) installBox.style.display = 'block';
    });

    if (installBtn) {
        installBtn.addEventListener('click', async function () {
            if (!deferredPrompt) {
                // iOS / navigation non-supportée : guide manuel
                alert('للتثبيت: اضغط زر المشاركة ثم «إضافة إلى الشاشة الرئيسية»');
                return;
            }
            deferredPrompt.prompt();
            const choice = await deferredPrompt.userChoice;
            if (choice.outcome === 'accepted') {
                if (installBox) installBox.style.display = 'none';
            }
            deferredPrompt = null;
        });
    }

    window.addEventListener('appinstalled', function () {
        if (installBox) installBox.style.display = 'none';
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

.verify-result.already-scanned {
    background: #7f1d1d;
    border-color: #991b1b;
    color: #fff;
}

.verify-result.already-scanned .result-icon { color: #fecaca; }
.verify-result.already-scanned h5 { color: #fff; }
.verify-result.already-scanned .result-details { border: 2px solid #991b1b; }
.verify-result.already-scanned .result-row { border-bottom-color: #fee2e2; }
.verify-result.already-scanned .result-row span { color: #fecaca; }
.verify-result.already-scanned .result-row strong { color: #fff; }

.verify-result.reset {
    background: #eff6ff;
    border-color: #93c5fd;
}

.verify-result.reset .result-icon { color: #2563eb; }
.verify-result.reset h5 { color: #1d4ed8; }

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

/* ===== PWA install box ===== */
.pwa-install-box {
    background: linear-gradient(135deg, #082f57, #0b3d70);
    border-radius: 16px;
    padding: 12px 16px;
    color: #fff;
}

.pwa-install-content {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.pwa-install-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.pwa-install-text {
    flex: 1;
    min-width: 150px;
}

.pwa-install-text strong {
    display: block;
    font-size: 0.95rem;
}

.pwa-install-text span {
    font-size: 0.75rem;
    opacity: 0.85;
}

.pwa-install-content .verify-btn {
    margin-top: 0;
    background: #fff;
    color: #082f57;
    white-space: nowrap;
    padding: 10px 18px;
    font-size: 0.82rem;
}
</style>
@endpush
