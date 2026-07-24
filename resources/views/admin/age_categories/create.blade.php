@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl">

    <h4 class="mb-3">➕ إضافة فئة عمرية</h4>

    <form action="{{ route('age-categories.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>الاسم</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>من عمر</label>
                <input type="number" name="min_age" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>إلى عمر</label>
                <input type="number" name="max_age" class="form-control" required>
            </div>
        </div>

        <button class="btn btn-success">💾 حفظ</button>
        <a href="{{ route('age-categories.index') }}" class="btn btn-secondary">رجوع</a>
    </form>

</div>
@endsection
