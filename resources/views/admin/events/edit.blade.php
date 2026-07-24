@extends('layouts.app')

@section('content')

@php
    $storageUrl = app()->environment('local')
        ? '/storage'
        : rtrim(env('PUBLIC_STORAGE_URL'), '/');
@endphp

<div class="container py-4" style="direction: rtl; text-align:right;">

    <h4 class="mb-4 fw-bold">✏️ تعديل حدث</h4>

    {{-- ================= GLOBAL ERRORS ================= --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>❌ فشل حفظ التعديلات:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('events.update', $event->id) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- ================= LEFT : MAIN INFO ================= --}}
            <div class="col-12 col-lg-8">

                {{-- INFO CARD --}}
                <div class="card-2026">
                    <h6 class="card-title mb-3">📌 معلومات الحدث</h6>

                    {{-- TITLE --}}
                    <div class="mb-3">
                        <label class="form-label">عنوان الحدث</label>
                        <input type="text"
                               name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $event->title) }}"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="mb-3">
                        <label class="form-label">وصف الحدث</label>
                        <textarea name="description"
                                  rows="5"
                                  class="form-control @error('description') is-invalid @enderror"
                                  required>{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- DATES CARD --}}
                <div class="card-2026 mt-4">
                    <h6 class="card-title mb-3">📅 تواريخ الحدث</h6>

                    <div class="row g-3">

                        {{-- START DATE --}}
                        <div class="col-md-6">
                            <label class="form-label">تاريخ البداية</label>
                            <input type="text"
                                   name="start_date"
                                   class="form-control js-date-fr @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date', optional($event->start_date)->format('Y-m-d')) }}"
                                   required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- END DATE --}}
                        <div class="col-md-6">
                            <label class="form-label">تاريخ النهاية</label>
                            <input type="text"
                                   name="end_date"
                                   class="form-control js-date-fr @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date', optional($event->end_date)->format('Y-m-d')) }}"
                                   required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

            </div>

            {{-- ================= RIGHT : IMAGE + ACTIVE ================= --}}
            <div class="col-12 col-lg-4">

                {{-- IMAGE CARD --}}
      <div class="card-2026 text-center">
    <h6 class="card-title mb-3">🖼️ صورة الحدث</h6>

    {{-- Image preview (centered & circular) --}}
    <div class="d-flex justify-content-center align-items-center mb-3 image-preview-circle">
        @if($event->image)
            <img id="imagePreview"
                 src="{{ asset($event->image) }}"
                 alt="Event image"
                 class="rounded-circle shadow-sm"
                 style="width:120px;height:120px;object-fit:cover;">
        @else
            <img id="imagePreview"
                 src="{{ asset('images/placeholder.png') }}"
                 alt="No image"
                 class="rounded-circle shadow-sm"
                 style="width:120px;height:120px;object-fit:cover;">
        @endif
    </div>

    {{-- File input --}}
    <input type="file"
           name="image"
           id="imageInput"
           class="form-control @error('image') is-invalid @enderror"
           accept="image/*">

    @error('image')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    <small class="text-muted d-block mt-2">
        إذا لم تغيّر الصورة ستبقى الحالية
    </small>
</div>


                {{-- ACTIVE CARD --}}
                <div class="card-2026 mt-4">
                    <h6 class="card-title mb-3">⚙️ حالة النشر</h6>

                    <div class="form-check form-switch">
                        <input class="form-check-input"
                               type="checkbox"
                               id="is_active"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $event->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_active">
                            تفعيل الحدث
                        </label>
                    </div>

                    <small class="text-muted">
                        عند التعطيل لن يظهر الحدث في الواجهة العامة
                    </small>
                </div>

            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="mt-4">
            <button class="btn btn-success px-4">💾 حفظ التعديلات</button>
            <a href="{{ route('events.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

    </form>
</div>

{{-- ================= STYLE 2026 ================= --}}
<style>
.card-2026{
    background:#fff;
    border-radius:20px;
    padding:22px;
    box-shadow:0 12px 30px rgba(0,0,0,.08);
}
.card-title{
    font-weight:800;
}
.image-preview-circle{
    width:150px;
    height:150px;
    border-radius:50%;
    overflow:hidden;
    border:2px dashed #0a4f88;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
}
.image-preview-circle img{
    width:100%;
    height:100%;
    object-fit:cover;
}
</style>

{{-- ================= JS ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('imageInput');
    const preview = document.getElementById('imagePreview');

    if(!input || !preview) return;

    input.addEventListener('change', function () {
        if (!this.files || !this.files[0]) return;

        const file = this.files[0];
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(file);
    });
});
</script>

@endsection
