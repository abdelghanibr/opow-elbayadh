@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9 col-12">

            <div class="card-modern shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-warning text-white text-center py-4">
                  <h3 class="fw-bold mb-0">✏ تعديل التخصص</h3> 
                        <p class="small opacity-75 mt-2">تحديث بيانات التخصص: <strong>{{ $activity->title }}</strong></p>
                </div>

                <div class="card-body p-4 p-md-5">

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4 shadow-sm mb-4">
                            <ul class="mb-0 fw-semibold">
                                @foreach ($errors->all() as $error)
                                    <li>⚠ {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.activities.update', $activity->id) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          id="activityForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">

                            <!-- اسم النشاط -->
                            <div class="col-12">
                                <label for="title" class="form-label fw-semibold text-primary">
                                  إسم التخصص <span class="text-danger">*</span>n>
                                </label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       value="{{ old('title', $activity->title) }}"
                                       class="form-control form-control-modern @error('title') is-invalid @enderror"
                                       placeholder="مثال: كرة القدم، السباحة"
                                       required>
                                @error('title')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- نوع النشاط -->
                            <div class="col-md-6">
                                <label for="activity_category_id" class="form-label fw-semibold text-primary">
                                    نوع الرياضة
                                </label>
                                <select name="activity_category_id"
                                        id="activity_category_id"
                                        class="form-select form-control-modern @error('activity_category_id') is-invalid @enderror">
                                              <option value="">— اختر نوع الرياضة —</option>
                                    @foreach($activityCategories as $cat)
                                        <option value="{{ $cat->id }}"
                                                {{ old('activity_category_id', $activity->activity_category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('activity_category_id')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- حالة النشاط -->
                            <div class="col-md-6">
                                <label for="is_active" class="form-label fw-semibold text-primary">
                                حالة نشط/غير نشط
                                </label>
                                <select name="is_active"
                                        id="is_active"
                                        class="form-select form-control-modern @error('is_active') is-invalid @enderror">
                                    <option value="1" {{ old('is_active', $activity->is_active) == 1 ? 'selected' : '' }}>نشط</option>
                                    <option value="0" {{ old('is_active', $activity->is_active) == 0 ? 'selected' : '' }}>غير نشط</option>
                                </select>
                                @error('is_active')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- اللون المميز -->
                            <div class="col-md-6">
                                <label for="color" class="form-label fw-semibold text-primary">
                                    اللون المميز للنشاط
                                </label>
                                <input type="color"
                                       name="color"
                                       id="color"
                                       value="{{ old('color', $activity->color ?? '#4361ee') }}"
                                       class="form-control form-control-color @error('color') is-invalid @enderror"
                                       style="height: 58px;">
                                @error('color')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold text-primary">
                                    الوصف (اختياري)
                                </label>
                                <textarea name="description"
                                          id="description"
                                          rows="4"
                                          class="form-control form-control-modern @error('description') is-invalid @enderror"
                                          placeholder="وصف مختصر عن النشاط...">{{ old('description', $activity->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- معاينة الأيقونة الحالية -->
                            <div class="col-12 text-center mb-4">
                                <div class="icon-preview-wrapper position-relative d-inline-block">
                                    <img id="iconPreview"
                                         src="{{ $activity->icon ? asset($activity->icon) : asset('images/default-activity-icon.png') }}"
                                         alt="معاينة الأيقونة"
                                         class="icon-circle shadow-lg">
                                    <div class="icon-overlay position-absolute top-50 start-50 translate-middle text-white fw-bold opacity-0">
                                        تغيير
                                    </div>
                                </div>
                                <p class="text-muted small mt-3">الأيقونة الحالية (اترك الحقل فارغًا للاحتفاظ بها)</p>
                            </div>

                            <!-- تحميل أيقونة جديدة -->
                            <div class="col-12">
                                <label for="icon" class="form-label fw-semibold text-primary">
                                    صورة جديدة للتخصص إختياري
                                </label>
                                <input type="file"
                                       name="icon"
                                       id="icon"
                                       accept="image/*"
                                       class="form-control form-control-modern @error('icon') is-invalid @enderror"
                                       onchange="previewIcon(this)">
                                <small class="text-muted d-block mt-2">
                                    الصيغ المسموحة: JPG, PNG, WebP — الحجم الأقصى: 4 ميغابايت — الأبعاد المثالية: 256×256 بكسل
                                </small>
                                @error('icon')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <!-- الأزرار -->
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-end mt-5">
                            <a href="{{ route('admin.activities.index') }}"
                               class="btn btn-outline-secondary btn-lg rounded-pill px-5 shadow-sm order-sm-2">
                                رجوع
                            </a>
                            <button type="submit"
                                    class="btn btn-warning btn-lg rounded-pill px-5 shadow-lg btn-glow-warning order-sm-1">
                                💾 حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

{{-- ======================== STYLES 2026 ======================== --}}
@push('css')
<style>
    :root {
        --primary: #4361ee;
        --primary-gradient: linear-gradient(135deg, #4361ee, #4cc9f0);
        --warning-gradient: linear-gradient(135deg, #ffb302, #ffcc3d);
        --glass: rgba(255, 255, 255, 0.2);
        --shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        --border-glass: 1px solid rgba(255, 255, 255, 0.3);
    }

    body {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .card-modern {
        background: var(--glass);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: var(--border-glass);
        box-shadow: var(--shadow);
    }

    .bg-gradient-warning {
        background: var(--warning-gradient);
    }

    .form-control-modern,
    .form-select {
        background: rgba(255, 255, 255, 0.7);
        border: none;
        border-radius: 1rem;
        padding: 0.9rem 1.2rem;
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .form-control-modern:focus,
    .form-select:focus {
        background: white;
        transform: translateY(-3px);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.2);
    }

    .form-control-color {
        width: 100%;
        height: 58px;
        padding: 0.5rem;
        cursor: pointer;
    }

    .btn-glow-warning {
        background: var(--warning-gradient);
        color: white;
        transition: all 0.4s ease;
    }

    .btn-glow-warning:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(255, 179, 2, 0.4);
        color: white;
    }

    /* معاينة الأيقونة */
    .icon-preview-wrapper {
        width: 140px;
        height: 140px;
    }

    .icon-circle {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border-radius: 50%;
        border: 6px solid white;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        transition: all 0.4s ease;
    }

    .icon-preview-wrapper:hover .icon-circle {
        transform: scale(1.08);
    }

    .icon-overlay {
        font-size: 1.1rem;
        transition: opacity 0.3s ease;
    }

    .icon-preview-wrapper:hover .icon-overlay {
        opacity: 1;
    }

    @media (max-width: 576px) {
        .icon-preview-wrapper,
        .icon-circle {
            width: 120px !important;
            height: 120px !important;
        }
        .card-body { padding: 2rem !important; }
    }
</style>
@endpush

{{-- ======================== SCRIPT PREVIEW ICON ======================== --}}
@push('js')
<script>
function previewIcon(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('iconPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush