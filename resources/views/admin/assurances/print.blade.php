<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة التأمينات</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: "Cairo", Arial, sans-serif; margin: 20px; color: #222; }
        h2 { text-align: center; margin-bottom: 8px; }
        .meta { text-align: center; font-size: .85rem; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: .82rem; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: center; }
        th { background: #0d6efd; color: #fff; font-weight: 600; }
        tr:nth-child(even) { background: #f8f9fa; }
        .badge { padding: 2px 8px; border-radius: 12px; font-size: .72rem; font-weight: 600; }
        .badge.pending { background: #fff3cd; color: #856404; }
        .badge.assured { background: #d1e7dd; color: #0f5132; }
        .print-footer { text-align: center; margin-top: 30px; font-size: .75rem; color: #999; }
        @media print { body { margin: 10mm; } }
    </style>
</head>
<body>
    <h2>قائمة التأمينات</h2>
    <p class="meta">عدد السجلات: {{ $assurances->count() }} — تاريخ الطباعة: {{ now()->format('Y/m/d H:i') }}</p>

    @if($assurances->isEmpty())
        <p style="text-align:center; color:#999;">لا توجد سجلات للطباعة.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم واللقب</th>
                    <th>الهاتف</th>
                    <th>تاريخ الميلاد</th>
                    <th>المجمع</th>
                    <th>فترة الحجز</th>
                    <th>فترة التأمين</th>
                    <th>الحالة</th>
                    <th>النوع</th>
                    <th>تاريخ الطباعة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assurances as $a)
                    <tr>
                        <td>{{ $a->id }}</td>
                        <td>{{ optional($a->person)->firstname }} {{ optional($a->person)->lastname }}</td>
                        <td>{{ optional($a->person)->phone ?? '---' }}</td>
                        <td>{{ optional($a->person)->birth_date ? \Carbon\Carbon::parse(optional($a->person)->birth_date)->format('Y/m/d') : '---' }}</td>
                        <td>{{ optional(optional($a->person)->user)->complex->nom ?? '---' }}</td>
                        <td>
                            {{ optional($a->reservation)->start_date ? \Carbon\Carbon::parse(optional($a->reservation)->start_date)->format('Y/m/d') : '---' }}
                            —
                            {{ optional($a->reservation)->end_date ? \Carbon\Carbon::parse(optional($a->reservation)->end_date)->format('Y/m/d') : '---' }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($a->start_date)->format('Y/m/d') }} — {{ \Carbon\Carbon::parse($a->end_date)->format('Y/m/d') }}</td>
                        <td><span class="badge {{ $a->status }}">{{ $a->status === 'pending' ? 'معني بالتأمين' : ($a->status === 'assured' ? 'مؤمن' : ($a->status === 'expired' ? 'منتهي' : 'ملغى')) }}</span></td>
                        <td>{{ $a->operation_type === 'new' ? 'تسجيل جديد' : 'تجديد' }}</td>
                        <td>{{ $a->printed_at ? \Carbon\Carbon::parse($a->printed_at)->format('Y/m/d H:i') : now()->format('Y/m/d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="print-footer">
        <p>بلدية البيض — إدارة التأمينات</p>
        <button onclick="window.print()" style="margin-top:10px; padding:8px 20px; border:none; background:#0d6efd; color:#fff; border-radius:8px; cursor:pointer;">🖨️ طباعة</button>
    </div>
</body>
</html>
