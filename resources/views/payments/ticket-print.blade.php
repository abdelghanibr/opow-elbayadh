<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تذكرة - #{{ $ticket->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; background: #fff; }

        .cut-page {
            width: 20cm;
            height: 10cm;
            position: relative;
            page-break-after: always;
            margin: 0 auto;
        }

        /* Cut lines — dashed border around the ticket */
        .cut-line-top {
            position: absolute;
            top: 0; left: 0; right: 0;
            border-top: 2px dashed #bbb;
        }

        .cut-line-bottom {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            border-bottom: 2px dashed #bbb;
        }

        .cut-line-left {
            position: absolute;
            top: 0; bottom: 0; left: 0;
            border-left: 2px dashed #bbb;
        }

        .cut-line-right {
            position: absolute;
            top: 0; bottom: 0; right: 0;
            border-right: 2px dashed #bbb;
        }

        /* Scissors icons */
        .scissors {
            position: absolute;
            font-size: 0.8rem;
            color: #999;
            background: #fff;
            padding: 0 3px;
            z-index: 2;
        }

        .scissors-tl { top: -6px; left: 4px; }
        .scissors-tr { top: -6px; right: 4px; }
        .scissors-bl { bottom: -6px; left: 4px; }
        .scissors-br { bottom: -6px; right: 4px; }

        .ticket {
            position: absolute;
            top: 6px;
            left: 6px;
            right: 6px;
            bottom: 6px;
            background: #fff;
            border-radius: 0;
            overflow: hidden;
            display: flex;
        }

        .ticket-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-left: 2px dashed #e2e8f0;
        }

        .ticket-right {
            width: 9cm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 10px 16px;
            text-align: right;
        }

        .match-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .team { text-align: center; flex: 1; }

        .team-logo {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: contain;
            border: 3px solid #e2e8f0;
            padding: 2px;
            background: #fff;
        }

        .team-name {
            font-weight: 800;
            font-size: 0.7rem;
            color: #082f57;
            margin-top: 3px;
            line-height: 1.1;
        }

        .vs-badge {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff;
            font-weight: 900;
            font-size: 0.65rem;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .match-meta {
            display: flex;
            gap: 12px;
            font-size: 0.65rem;
            color: #64748b;
            margin-bottom: 10px;
            justify-content: center;
        }

        .match-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .qr-code {
            border: 3px solid #082f57;
            border-radius: 10px;
            padding: 4px;
            display: inline-block;
            background: #fff;
        }

        .opow-name {
            font-weight: 800;
            font-size: 0.7rem;
            color: #082f57;
            margin-top: 8px;
            text-align: center;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .detail-row:last-child { border-bottom: none; }

        .detail-label {
            font-size: 0.65rem;
            color: #94a3b8;
            font-weight: 700;
        }

        .detail-value {
            font-size: 0.75rem;
            color: #082f57;
            font-weight: 800;
        }

        .print-date {
            font-size: 0.55rem;
            color: #94a3b8;
            margin-top: 8px;
            text-align: center;
        }

        @media print {
            @page { size: 20cm 10cm; margin: 0; }
            body { background: #fff; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .cut-page { margin: 0; }
        }

        .print-btn-container {
            text-align: center;
            margin-top: 24px;
        }

        .print-btn {
            background: linear-gradient(135deg, #082f57, #0b3d70);
            color: #fff;
            border: none;
            padding: 14px 32px;
            border-radius: 14px;
            font-family: 'Cairo', sans-serif;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(8,47,87,0.3);
            color: #fff;
        }
    </style>
</head>
<body>

<div>
    <div class="cut-page">
        <div class="cut-line-top"></div>
        <div class="cut-line-bottom"></div>
        <div class="cut-line-left"></div>
        <div class="cut-line-right"></div>

        <span class="scissors scissors-tl">&#9986;</span>
        <span class="scissors scissors-tr">&#9986;</span>
        <span class="scissors scissors-bl">&#9986;</span>
        <span class="scissors scissors-br">&#9986;</span>

        <div class="ticket">
            <div class="ticket-left">
                @if($ticket->qr_code)
                    <div class="qr-code">
                        {!! QrCode::size(110)->encoding('UTF-8')->generate($ticket->qr_code) !!}
                    </div>
                @endif
                <div class="print-date">{{ now()->format('Y-m-d H:i') }}</div>
            </div>

            <div class="ticket-right">
                <div class="match-header">
                    <div class="team">
                        <img src="{{ asset($ticket->match->homeTeam->logo ?? 'images/default-team.png') }}" alt="{{ $ticket->match->homeTeam->name }}" class="team-logo">
                        <div class="team-name">{{ $ticket->match->homeTeam->name }}</div>
                    </div>
                    <div class="vs-badge">VS</div>
                    <div class="team">
                        <img src="{{ asset($ticket->match->awayTeam->logo ?? 'images/default-team.png') }}" alt="{{ $ticket->match->awayTeam->name }}" class="team-logo">
                        <div class="team-name">{{ $ticket->match->awayTeam->name }}</div>
                    </div>
                </div>

                <div class="match-meta">
                    <span><i class="fa-solid fa-calendar-days"></i> {{ $ticket->match->match_date }}</span>
                    <span><i class="fa-solid fa-clock"></i> {{ $ticket->match->match_time }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">رقم التذكرة</span>
                    <span class="detail-value">#{{ $ticket->id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">الاسم</span>
                    <span class="detail-value">{{ $ticket->buyer_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">نوع التذكرة</span>
                    <span class="detail-value">{{ $ticket->seatType->name }}</span>
                </div>

                <div class="opow-name">{{ $settings['office_short'] ?? 'OPOW EL BAYADH' }}</div>
            </div>
        </div>
    </div>

    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print()">
            <i class="fa-solid fa-print"></i>
            طباعة
        </button>
        <a href="{{ route('ticket.show-result', $ticket->id) }}" class="print-btn" style="background: linear-gradient(135deg, #64748b, #475569); margin-inline-start: 12px;">
            <i class="fa-solid fa-arrow-right"></i>
            رجوع
        </a>
    </div>
</div>

</body>
</html>
