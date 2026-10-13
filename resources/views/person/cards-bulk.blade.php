<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>بطاقات لاعبين — {{ $entityName }} — {{ $seasonLabel }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    @include('person.partials.card-css')

    <style>
        /* ===== Écran : une seule page A4 (la 1ère) ===== */
        .bulk-page { display: none; }
        .bulk-page:first-of-type { display: grid; }
        .note-box { max-width: 640px; }

        /* ===== Impression A4 : 8 cartes par page (2 cols × 4 rows) ===== */
        @media print {
            @page { size: A4 portrait; margin: 3mm; }
            body { background: #fff !important; padding: 12px; display: block; align-items: stretch; min-height: auto; }
            .actions, .note-box, .bulk-head { display: none !important; }

            .bulk-page {
                display: grid !important;
                grid-template-columns: repeat(2, 95mm);
                grid-template-rows: repeat(4, 55mm);
                gap: 0;
                width: 190mm;
                margin: 0 auto;
                align-content: start;
            }

            .bulk-page:not(:last-of-type) { page-break-after: always; }

            .bulk-page .card-piece {
                box-shadow: none;
                border-radius: 2mm;
                border: 0.2mm solid #eee;
            }
            .card-piece { break-inside: avoid; }

            .badge, .badge-top, .badge-body, .badge-foot {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .bulk-head {
            width: 190mm;
            max-width: 100%;
            margin: 0 auto 18px;
            text-align: center;
            color: #10330f;
        }
        .bulk-head h2 { font-size: 20px; font-weight: 800; }
        .bulk-head p { font-size: 13px; color: #46624a; font-weight: 600; }
    </style>
</head>
<body>

    {{-- Boutons (écran uniquement) --}}
    <div class="actions">
        <a href="{{ $backUrl }}">🔙 رجوع</a>
        <button class="btn-outline" onclick="window.print()">🖨️ طباعة الكل ({{ $cards->count() }})</button>
        <button onclick="savePdf()">⬇ تحميل PDF</button>
    </div>

    {{-- En-tête résumé (écran uniquement) --}}
    <div class="bulk-head">
        <h2>🪪 بطاقات اللاعبين — {{ $entityName }}</h2>
        <p>الموسم الرياضي : {{ $seasonLabel }} — العدد: {{ $cards->count() }} — تُطبع 8 بطاقات في كل صفحة A4</p>
    </div>

    {{-- Pages de 8 cartes : 2 colonnes × 4 lignes --}}
    @foreach($cards->chunk(8) as $page)
        <div class="bulk-page">
            @foreach($page as $card)
                @include('person.partials.card-piece', ['card' => $card])
            @endforeach
        </div>
    @endforeach

    @if($cards->isEmpty())
        <div class="note-box">
            ⚠️ لا يوجد أي لاعب مسجل في {{ $entityName }} بعد.
        </div>
    @endif

    <script>
        function savePdf() {
            window.print();
        }
    </script>

</body>
</html>