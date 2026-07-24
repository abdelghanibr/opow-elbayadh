@extends('layouts.app')

@section('content')
<div class="container" style="direction: rtl">

<h4 class="fw-bold mb-3">✏️ تعديل نوع الرياضة</h4>

<form method="POST"
      action="{{ route('activity-categories.update',$activityCategory) }}">
@csrf @method('PUT')

<div class="mb-3">
      <label class="fw-bold">اسم النوع</label>
    <input name="name" class="form-control"
           value="{{ $activityCategory->name }}" required>
</div>



<div class="mb-3">
    <label class="fw-bold">اللون</label>
    <input name="color" type="color"
           class="form-control form-control-color"
           value="{{ $activityCategory->color }}">
</div>

<button class="btn btn-warning">💾 تحديث</button>
<a href="{{ route('activity-categories.index') }}"
   class="btn btn-secondary">رجوع</a>

</form>
</div>
@endsection
