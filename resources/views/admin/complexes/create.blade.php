@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="card-modern shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header text-center py-4">
                    <h3 class="fw-bold mb-0">إضافة منشأة رياضية جديدة</h3>
                    <p class="small text-secondary mt-2">
                        يرجى إدخال بيانات المنشأة الرياضية بشكل صحيح
                    </p>
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

                    <form action="{{ route('admin.complexes.store') }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">
                            <!-- اسم المنشأة -->
                            <div class="col-12">
                                <label class="form-label">اسم المنشأة الرياضية *</label>
                                <input type="text"
                                       name="nom"
                                       value="{{ old('nom') }}"
                                       class="form-control form-control-modern @error('nom') is-invalid @enderror"
                                       placeholder="مثال: ملعب محمد بوضياف"
                                       required>
                                @error('nom')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- نوع المنشأة -->
                            <div class="col-12">
                                <label class="form-label fw-bold">نوع المنشأة الرياضية</label>
                                <select name="type"
                                        class="form-control form-control-modern @error('type') is-invalid @enderror"
                                        required>
                                    <option value="">— اختر نوع المنشأة —</option>
                                    <option value="swimming" {{ old('type') == 'swimming' ? 'selected' : '' }}>
                                        🏊‍♂️ مسبح
                                    </option>
                                    <option value="stadium" {{ old('type') == 'stadium' ? 'selected' : '' }}>
                                        ⚽ ملعب
                                    </option>
                                    <option value="hall" {{ old('type') == 'hall' ? 'selected' : '' }}>
                                        🏋️‍♂️ قاعة رياضية
                                    </option>
                                </select>
                                @error('type')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- العنوان -->
                            <div class="col-12">
                                <label class="form-label">العنوان</label>
                                <input type="text"
                                       name="adresse"
                                       value="{{ old('adresse') }}"
                                       class="form-control form-control-modern @error('adresse') is-invalid @enderror"
                                       placeholder="العنوان الكامل">
                                @error('adresse')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       class="form-control form-control-modern @error('phone') is-invalid @enderror"
                                       placeholder="+213 000 00 00 00">
                                @error('phone')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- طاقة البالغين -->
                            <div class="col-md-6">
                                <label class="form-label">الطاقة الاستيعابية للبالغين</label>
                                <input type="number"
                                       name="capacite_ma"
                                       value="{{ old('capacite_ma') }}"
                                       class="form-control form-control-modern @error('capacite_ma') is-invalid @enderror"
                                       placeholder="مثال: 100">
                                @error('capacite_ma')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- طاقة القاصرين -->
                            <div class="col-md-6">
                                <label class="form-label">الطاقة الاستيعابية للقصر</label>
                                <input type="number"
                                       name="capacite_mi"
                                       value="{{ old('capacite_mi') }}"
                                       class="form-control form-control-modern @error('capacite_mi') is-invalid @enderror"
                                       placeholder="مثال: 50">
                                @error('capacite_mi')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload Image - Style moderne 2026 -->
                            <div class="col-12 text-center">
                                <label class="form-label mb-3 fw-semibold">📸 صورة المنشأة الرياضية</label>

                                <div class="image-upload-container mx-auto">
                                    <div class="image-circle-wrapper">
                                        <div class="image-circle" role="button" tabindex="0"
                                             onclick="document.getElementById('image').click()">
                                            <img id="imagePreview"
                                                 src="{{ asset('images/placeholder.png') }}"
                                                 alt="معاينة الصورة">
                                            <div class="image-overlay">
                                                <span>اختيار صورة</span>
                                                <i class="fas fa-camera mt-1"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="file"
                                           name="image"
                                           id="image"
                                           accept="image/png,image/jpeg,image/webp"
                                           class="d-none"
                                           onchange="previewImage(this)"
                                           required>
                                    @error('image')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted">
                                        الحد الأقصى: 5 ميغابايت • png, jpg, webp<br>
                                        اضغط على الدائرة لاختيار صورة
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-end mt-5">
                            <a href="{{ route('admin.complexes.index') }}"
                               class="btn btn-outline-secondary btn-lg rounded-pill px-5 shadow-sm">
                                رجوع
                            </a>
                            <button type="submit"
                                    class="btn btn-warning btn-lg rounded-pill px-5 shadow-lg btn-glow-warning">
                                💾 حفظ المنشأة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
/* ===== Thème Clair 2026 - Light Mode Premium ===== */
:root {
    --bg-light: #f8fafc;
    --card-bg: #ffffff;
    --input-bg: #ffffff;
    --input-border: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --label-color: #3b82f6;
    --accent: #3b82f6;
    --warning-gradient: linear-gradient(135deg, #f59e0b, #fbbf24);
    --shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    --border: 1px solid #e2e8f0;
}

.card-modern {
    background: var(--card-bg);
    border: var(--border);
    box-shadow: var(--shadow);
    border-radius: 1rem;
}

.form-label {
    color: var(--label-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-control-modern,
.form-select {
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    color: var(--text-primary);
    border-radius: 0.75rem;
    padding: 0.85rem 1.1rem;
    transition: all 0.3s ease;
}

.form-control-modern:focus,
.form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
}

.btn-glow-warning {
    background: var(--warning-gradient);
    color: white;
    border: none;
    transition: all 0.4s ease;
}

.btn-glow-warning:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
}

/* ─── Image Upload - Modern Circle 2026 ─── */
.image-upload-container {
    position: relative;
    width: 100%;
    max-width: 240px;
    margin: 0 auto;
}

.image-circle-wrapper {
    position: relative;
    padding-top: 100%; /* ratio 1:1 */
}

.image-circle {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    overflow: hidden;
    background: #f1f5f9;
    border: 3px dashed #cbd5e1;
    transition: all 0.35s ease;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.image-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.image-circle:hover {
    border-color: var(--accent);
    box-shadow: 0 12px 35px rgba(59, 130, 246, 0.25);
    transform: scale(1.04);
}

.image-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.35s ease;
    backdrop-filter: blur(2px);
}

.image-circle:hover .image-overlay {
    opacity: 1;
}

.image-overlay span {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.35rem;
}

.image-overlay i {
    font-size: 1.6rem;
}

/* Responsive */
@media (max-width: 576px) {
    .image-upload-container {
        max-width: 200px;
    }
    .image-overlay span {
        font-size: 0.95rem;
    }
}

@media (max-width: 400px) {
    .image-upload-container {
        max-width: 170px;
    }
}
</style>
@endpush

@push('js')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        if (input.files[0].size > 5 * 1024 * 1024) {
            alert('حجم الصورة كبير جدًا! الحد الأقصى 5 ميغابايت');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush