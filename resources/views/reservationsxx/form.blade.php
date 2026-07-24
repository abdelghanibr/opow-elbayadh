@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right">

    <h3 class="mb-4">نموذج حجز المركبة: {{ $complex->nom }}</h3>

    <div class="card p-4">

        <p><strong>السعة:</strong> {{ $complex->capacite }} شخص</p>
        <p><strong>السعر:</strong> {{ number_format($complex->prix, 2) }} دج</p>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('reservation.store') }}" method="POST">
            @csrf

            <input type="hidden" name="complex_id" value="{{ $complex->id }}">

            <div class="mb-3">
                <label class="form-label">تاريخ الحجز</label>
                <input type="text" name="date_reservation" class="form-control js-date-fr" required>
            </div>

            <div class="mb-3">
                <label class="form-label">وقت البداية</label>
                <input type="time" name="heure_debut" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">وقت النهاية</label>
                <input type="time" name="heure_fin" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">تأكيد الحجز</button>
        </form>
    </div>

</div>
@endsection
