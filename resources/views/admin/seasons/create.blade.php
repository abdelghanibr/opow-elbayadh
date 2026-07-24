@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; max-width:700px">

    <h3 class="fw-bold mb-4">➕ إضافة موسم</h3>

    <form action="{{ route('seasons.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="fw-bold">اسم الموسم</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="fw-bold">تاريخ البداية</label>
                <input type="text" name="date_debut" class="form-control js-date-fr" required>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">تاريخ النهاية</label>
                <input type="text" name="date_fin" class="form-control js-date-fr" required>
            </div>
        </div>

      <div class="mb-3">
    <label class="fw-bold">نوع الموسم</label>
    <select name="type_season" class="form-control" required>
        @foreach($types as $key=>$label)
            <option value="{{ $key }}">{{ $label }}</option>
        @endforeach
    </select>
</div>

        <button class="btn btn-success fw-bold w-100 mt-3">💾 حفظ</button>
    </form>
</div>
@endsection
