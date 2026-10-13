<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $card['title'] }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    @include('person.partials.card-css')

    <style>
        /* ===== Impression A4 : une seule carte, centrée ===== */
        @media print {
            @page { size: A4 portrait; margin: 3mm; }
            body { background: #fff !important; padding: 0 !important; }
            .actions, .note-box { display: none !important; }

            .sheet {
                grid-template-columns: repeat(1, 95mm);
                gap: 0;
                margin: 0;
                min-height: calc(100vh - 6mm);
                align-content: center;
                width: 100%;
            }
            .card-piece { box-shadow: none; border-radius: 2.5mm; }
            .badge, .badge-top, .badge-body, .badge-foot {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="actions">
        @if(isset($backUrl) && $backUrl)
            <a href="{{ $backUrl }}">🔙 رجوع</a>
        @else
            <a href="{{ route('person.card') }}">👁 معاينة البطاقة</a>
        @endif
        <button class="btn-outline" onclick="window.print()">🖨️ طباعة البطاقة</button>
        <button onclick="savePdf()">⬇ تحميل PDF</button>
    </div>

    @if($card['canIssue'])
        <p style="font-size:12px;color:#4b6b4d;font-weight:600">🪪 حجم البطاقة: 95 × 55 ملم — تُطبع بطاقة واحدة في الصفحة</p>
    @endif

    {{-- 10 exemplaires (écran : seule la 1ère visible) --}}
    <div class="sheet">
        @for($i = 0; $i < 10; $i++)
            @include('person.partials.card-piece', ['card' => $card])
        @endfor
    </div>

    @if(! $card['canIssue'])
        <div class="note-box">
            ⚠️ البطاقة غير قابلة للإصدار حالياً:
            <ul>
                @foreach($card['blockers'] as $b)
                    <li>{{ $b }}</li>
                @endforeach
                @foreach($card['warnings'] as $w)
                    <li>{{ $w }}</li>
                @endforeach
            </ul>
            <div style="margin-top:6px">
                <a href="{{ route('profile.step', 1) }}" style="color:#7a5b00; font-weight:800">✏️ إكمال بياناتي</a>
            </div>
        </div>
    @endif

    <script>
        function savePdf() {
            window.print();
        }
    </script>

</body>
</html>