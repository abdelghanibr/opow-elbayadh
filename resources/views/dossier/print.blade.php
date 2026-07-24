@extends('layouts.app')

@section('content')

<style>
.fiche-container {
    max-width: 800px;
    margin: auto;
    background: #fff;
    padding: 25px 30px;
    border: 1px solid #ddd;
}

.fiche-title {
    font-size: 20px;
    font-weight: 700;
    margin-top: 10px;
}

.section-title {
    font-weight: 700;
    margin-bottom: 6px;
    border-bottom: 2px solid #0a8a67;
    padding-bottom: 3px;
}

.info-table th {
    width: 30%;
    background: #f5f5f5;
    font-weight: 600;
}

.info-table th,
.info-table td {
    border: 1px solid #ccc;
    padding: 6px 8px;
}

.photo-box {
    border: 1px solid #ccc;
    padding: 4px;
    width: 120px;
    height: 150px;
    margin: auto;
}

.print-btn {
    margin-top: 20px;
}

.signature-box {
    border: 1px dashed #999;
    border-radius: 8px;
    margin-top: 8px;
    display: inline-block;
    background: #fafafa;
}

#signaturePad {
    display: block;
    cursor: crosshair;
    touch-action: none;
}

@media print {
    body * {
        visibility: hidden;
    }

    #fichePrintArea, #fichePrintArea * {
        visibility: visible;
    }

    #fichePrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .no-print {
        display: none !important;
    }
}
</style>

<div class="container py-4" style="direction: rtl">

    <div id="fichePrintArea" class="fiche-container">

        {{-- ENTETE --}}
        <div class="text-center mb-4">
            <img src="{{ asset('images/djs-logo.png') }}" style="width:70px">
            <div class="fw-bold">ديوان المركب المتعدد الرياضات</div>
            <div class="text-muted">ولاية البيض</div>
            <div class="fiche-title">استمارة التسجيل</div>
        </div>

        {{-- PERSON --}}
        <div class="row align-items-center mb-3">

            <div class="col-3 text-center">
                <div class="photo-box">
                    <img src="{{ $dossier->person->photo 
                                ? asset($dossier->person->photo) 
                                : asset('images/avatar.png') }}"
                         style="width:100%; height:100%; object-fit:cover;">
                </div>
            </div>

            <div class="col-9">
                <div class="section-title">معلومات المشترك</div>

                <table class="table table-sm info-table mb-0">
                    <tr>
                        <th>الاسم</th>
                        <td>{{ $dossier->person->firstname }}</td>
                    </tr>
                    <tr>
                        <th>اللقب</th>
                        <td>{{ $dossier->person->lastname }}</td>
                    </tr>
                    <tr>
                        <th>اسم الأب</th>
                        <td>{{ $dossier->person->tuteur_fullname ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>تاريخ الازدياد</th>
                        <td>{{ $dossier->person->birth_date ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>فصيلة الدم</th>
                        <td>{{ $dossier->person->blood_type ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>المهنة</th>
                        <td>{{ $dossier->person->profession ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>العنوان</th>
                        <td>{{ $dossier->person->address ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>الهاتف</th>
                        <td>{{ $dossier->person->phone ?? '—' }}</td>
                    </tr>
                    <tr>
    <th>البريد الإلكتروني</th>
    <td>{{ $dossier->person?->user?->email ?? '—' }}</td>
</tr>
                </table>
            </div>
        </div>

  

        {{-- COMPLEX --}}
        <div class="mb-3">
              <div class="section-title">المنشأة الرياضية</div>

            <table class="table table-sm info-table mb-0">
                <tr>
                   <th>اسم المنشأة</th>
                    <td>{{ $dossier->person?->user?->complex?->nom ?? '—' }}</td>

                </tr>
                <tr>
                    <th>العنوان</th>
                   <td>{{ $dossier->person?->user?->complex?->adresse ?? '—' }}</td>
                </tr>
            </table>
        </div>

        {{-- STATUS --}}
      @php
    $etat = $dossier->etat ?? 'pending';

    $etatLabels = [
        'pending'  => 'ملفكم قيد الدراسة',
        'approved' => 'ملفكم مقبول',
        'rejected' => 'ملفكم مرفوض',
    ];

    $etatClasses = [
        'pending'  => 'bg-warning text-dark',
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
    ];
@endphp

<div class="text-center mt-3">
    <span class="badge {{ $etatClasses[$etat] ?? 'bg-secondary' }} p-2">
        ملاحظة : {{ $etatLabels[$etat] ?? $etat }}
    </span>
</div>

        {{-- SIGNATURE --}}
        <div class="row mt-5 text-center">
            <div class="col">
                توقيع المعني
                <div class="signature-box">
                    <canvas id="signaturePad" width="280" height="100"></canvas>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger mt-1 no-print" onclick="clearSignature()">مسح التوقيع</button>
            </div>
            <div class="col">
                إدارة المركب
                <div style="height:100px; border-bottom:1px dashed #999;"></div>
            </div>
        </div>

        <input type="hidden" name="signature_data" id="signatureData">

        {{-- FOOTER --}}
        <div class="text-center text-muted mt-4 small">
            تم إنشاء هذه الوثيقة إلكترونيًا عبر منصة {{ config('app.name') }}<br>
            {{ now()->format('Y-m-d H:i') }}
        </div>

    </div>

    {{-- PRINT BUTTON --}}
    <div class="text-center print-btn no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ طباعة الاستمارة
        </button>
    </div>

</div>

<script>
(function() {
    const canvas = document.getElementById('signaturePad');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let drawing = false;
    let lastX, lastY;

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const touch = e.touches ? e.touches[0] : e;
        return {
            x: touch.clientX - rect.left,
            y: touch.clientY - rect.top
        };
    }

    function startDraw(e) {
        e.preventDefault();
        drawing = true;
        const pos = getPos(e);
        lastX = pos.x;
        lastY = pos.y;
    }

    function draw(e) {
        if (!drawing) return;
        e.preventDefault();
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(pos.x, pos.y);
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.stroke();
        lastX = pos.x;
        lastY = pos.y;
    }

    function stopDraw() {
        drawing = false;
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseleave', stopDraw);
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDraw);
})();

function clearSignature() {
    const canvas = document.getElementById('signaturePad');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}
</script>

@endsection
