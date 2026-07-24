@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right">

    <h3 class="mb-4">📌 حالة ملفك</h3>

    <div class="card p-4">

        <p><strong>الحالة الحالية:</strong></p>

        @if($dossier->etat == 'Validé')
            <div class="alert alert-success">
                ✔ تم قبول ملفك. يمكنك الآن القيام بالحجوزات.
            </div>
        @elseif($dossier->etat == 'Refusé')
            <div class="alert alert-danger">
                ✘ تم رفض ملفك. يرجى إعادة الرفع من جديد.
            </div>
        @else
            <div class="alert alert-warning">
                ⏳ ملفك قيد الدراسة. يرجى الانتظار.
            </div>
        @endif

    </div>
</div>
@endsection
