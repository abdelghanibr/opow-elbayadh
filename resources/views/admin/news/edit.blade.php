@extends('layouts.app')

@section('content')

@php
    $storageUrl = app()->environment('local')
        ? '/storage'
        : rtrim(env('PUBLIC_STORAGE_URL'), '/');
@endphp

<div class="container py-4" style="direction: rtl; text-align:right;">

    <h4 class="mb-4 fw-bold">✏️ تعديل خبر</h4>

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

    <form action="{{ route('news.update', $news->id) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- ================= LEFT : CONTENT ================= --}}
            <div class="col-12 col-lg-8">

                {{-- INFO CARD --}}
                <div class="card-2026">
                    <h6 class="card-title mb-3">📰 معلومات الخبر</h6>

                    {{-- TITLE --}}
                    <div class="mb-3">
                        <label class="form-label">عنوان الخبر</label>
                        <input type="text"
                               name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $news->title) }}"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- CONTENT --}}
                    <div class="mb-3">
                        <label class="form-label">محتوى الخبر</label>
                        <textarea name="content"
                                  rows="6"
                                  class="form-control @error('content') is-invalid @enderror"
                                  required>{{ old('content', $news->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ================= RIGHT : IMAGE + ACTIVE ================= --}}
  <div class="col-12 col-lg-4">

    {{-- IMAGE CARD --}}
    <div class="card-2026 text-center">
        <h6 class="card-title mb-3">🖼️ صورة الخبر</h6>

        {{-- Image preview --}}
        <div class="d-flex justify-content-center align-items-center mb-3 image-preview-circle">
            @if($news->image)
                <img id="imagePreview"
                     src="{{ asset($news->image) }}"
                     alt="News image"
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
                   {{ old('is_active', $news->is_active) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_active">
                نشر الخبر
            </label>
        </div>

        <small class="text-muted">
            عند التعطيل لن يظهر الخبر في الواجهة العامة
        </small>
    </div>
</div>


        </div>

        {{-- ACTIONS --}}
        <div class="mt-4">
            <button class="btn btn-primary px-4">💾 حفظ التعديلات</button>
            <a href="{{ route('news.index') }}" class="btn btn-secondary">رجوع</a>
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
        if (!file.type.startsWith('image/')) {
            alert('الملف المختار ليس صورة');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(file);
    });
});
</script>

@endsection
