@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="card-modern shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header text-center py-4">
                    <h3 class="fw-bold mb-0">✏️ تعديل بيانات المنشأة الرياضية</h3>
                    <p class="small text-secondary mt-2">
                        قم بتحديث بيانات المنشأة: <strong>{{ $complex->nom }}</strong>
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

                    <form action="{{ route('admin.complexes.update', $complex->id) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <!-- Nom du complexe -->
                            <div class="col-12">
                                <label class="form-label">اسم المنشأة الرياضية *</label>
                                <input type="text" name="nom"
                                       value="{{ old('nom', $complex->nom) }}"
                                       class="form-control form-control-modern @error('nom') is-invalid @enderror"
                                       placeholder="مثال: ملعب محمد بوضياف" required>
                                @error('nom')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Type de l'installation -->
                            <div class="col-12">
                                <label class="form-label fw-bold">نوع المنشأة الرياضية</label>
                                <select name="type"
                                        class="form-control form-control-modern @error('type') is-invalid @enderror"
                                        required>
                                    <option value="">— اختر نوع المنشأة —</option>
                                    <option value="swimming" {{ old('type', $complex->type) == 'swimming' ? 'selected' : '' }}>🏊‍♂️ مسبح</option>
                                    <option value="stadium"   {{ old('type', $complex->type) == 'stadium'   ? 'selected' : '' }}>⚽ ملعب</option>
                                    <option value="hall"      {{ old('type', $complex->type) == 'hall'      ? 'selected' : '' }}>🏋️‍♂️ قاعة رياضية</option>
                                </select>
                                @error('type')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Adresse -->
                            <div class="col-12">
                                <label class="form-label">العنوان</label>
                                <input type="text" name="adresse"
                                       value="{{ old('adresse', $complex->adresse) }}"
                                       class="form-control form-control-modern @error('adresse') is-invalid @enderror"
                                       placeholder="العنوان الكامل">
                                @error('adresse') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            </div>

                            <!-- Téléphone -->
                            <div class="col-md-6">
                                <label class="form-label">الهاتف</label>
                                <input type="text" name="phone"
                                       value="{{ old('phone', $complex->telephone) }}"
                                       class="form-control form-control-modern @error('phone') is-invalid @enderror"
                                       placeholder="+213 000 00 00 00">
                                @error('phone') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            </div>

                            <!-- Capacité adultes -->
                            <div class="col-md-6">
                                <label class="form-label">الطاقة الاستيعابية للبالغين</label>
                                <input type="number" name="capacite_ma"
                                       value="{{ old('capacite_ma', $complex->capacite_ma) }}"
                                       class="form-control form-control-modern @error('capacite_ma') is-invalid @enderror"
                                       placeholder="مثال: 100">
                                @error('capacite_ma') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            </div>

                            <!-- Capacité mineurs -->
                            <div class="col-md-6">
                                <label class="form-label">الطاقة الاستيعابية للقصر</label>
                                <input type="number" name="capacite_mi"
                                       value="{{ old('capacite_mi', $complex->capacite_mi) }}"
                                       class="form-control form-control-modern @error('capacite_mi') is-invalid @enderror"
                                       placeholder="مثال: 50">
                                @error('capacite_mi') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            </div>

                            <!-- Upload image - version renforcée -->
                            <div class="col-12 text-center">
                                <label class="form-label mb-3 fw-semibold">📸 صورة المنشأة الرياضية</label>

                                <div class="image-upload-container mx-auto">
                                    <div class="image-circle-wrapper">
                                        <div class="image-circle" role="button" tabindex="0"
                                             onclick="document.getElementById('image').click()">
                                            <img id="imagePreview"
                                                 src="{{ $complex->image ? asset($complex->image) : asset('images/placeholder.png') }}"
                                                 alt="معاينة صورة المنشأة">
                                            <div class="image-overlay">
                                                <span>تغيير الصورة</span>
                                                <i class="fas fa-camera mt-1"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="file" name="image" id="image"
                                           accept="image/png,image/jpeg,image/webp"
                                           class="d-none" onchange="previewImage(this)">
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted">
                                        الحد الأقصى: 5 ميغابايت • png, jpg, webp<br>
                                        اضغط على الصورة لتغييرها
                                    </small>
                                </div>

                                @error('image')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-end mt-5">
                            <a href="{{ route('admin.complexes.index') }}"
                               class="btn btn-outline-secondary btn-lg rounded-pill px-5 shadow-sm">
                                رجوع
                            </a>
                            <button type="submit"
                                    class="btn btn-warning btn-lg rounded-pill px-5 shadow-lg btn-glow-warning">
                                💾 تحديث البيانات
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
:root {
    --accent: #3b82f6;
    --warning-gradient: linear-gradient(135deg, #f59e0b, #fbbf24);
}

/* ─── Styles de base (rappel) ─── */
.form-control-modern,
.form-select {
    border-radius: 0.75rem;
    padding: 0.85rem 1.1rem;
    transition: all 0.3s ease;
}

/* ─── Image Upload - Priorité renforcée ─── */
.image-upload-container {
    position: relative;
    width: 100%;
    max-width: 240px;
    margin: 0 auto;
}

.image-circle-wrapper {
    position: relative;
    padding-top: 100%; /* ratio parfait 1:1 */
}

.image-circle {
    position: absolute !important;
    inset: 0 !important;
    border-radius: 50% !important;
    overflow: hidden !important;
    background: #f1f5f9 !important;
    border: 3px dashed #cbd5e1 !important;
    transition: all 0.35s ease !important;
    cursor: pointer !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
}

.image-circle img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center !important;
    display: block !important;
}

.image-circle:hover {
    border-color: var(--accent) !important;
    box-shadow: 0 12px 35px rgba(59, 130, 246, 0.28) !important;
    transform: scale(1.04) !important;
}

.image-overlay {
    position: absolute !important;
    inset: 0 !important;
    background: rgba(0, 0, 0, 0.55) !important;
    color: white !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: 0 !important;
    transition: opacity 0.35s ease !important;
    backdrop-filter: blur(2px) !important;
    z-index: 2 !important;
}

.image-circle:hover .image-overlay {
    opacity: 1 !important;
}

.image-overlay span {
    font-weight: 600 !important;
    font-size: 1.1rem !important;
    margin-bottom: 0.35rem !important;
}

.image-overlay i {
    font-size: 1.6rem !important;
}

/* Responsive - priorité renforcée */
@media (max-width: 576px) {
    .image-upload-container {
        max-width: 200px !important;
    }
    .image-overlay span {
        font-size: 0.95rem !important;
    }
}

@media (max-width: 400px) {
    .image-upload-container {
        max-width: 170px !important;
    }
}
</style>
@endpush

@push('js')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        if (input.files[0].size > 5 * 1024 * 1024) {
            alert('الصورة كبيرة جداً! الحد الأقصى 5 ميغابايت');
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