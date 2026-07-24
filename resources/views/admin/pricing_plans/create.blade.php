@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">

            <div class="card-modern shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white text-center py-4">
                    <h3 class="fw-bold mb-0">➕ إضافة خطة تسعير جديدة</h3>
                    <p class="small opacity-75 mt-2">أدخل تفاصيل الخطة التسعيرية</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.pricing_plans.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="row g-4">

                            <!-- النشاط -->
                            <div class="col-md-6">
                                <label for="activity_id" class="form-label fw-semibold text-primary">
                                    النشاط <span class="text-danger">*</span>
                                </label>
                                <select name="activity_id" id="activity_id"
                                        class="form-select form-control-modern @error('activity_id') is-invalid @enderror"
                                        required>
                                    <option value="">اختر النشاط</option>
                                    @foreach($activities as $a)
                                        <option value="{{ $a->id }}" {{ old('activity_id') == $a->id ? 'selected' : '' }}>
                                            {{ $a->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('activity_id')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الفئة العمرية -->
                            <div class="col-md-6">
                                <label for="age_category_id" class="form-label fw-semibold text-primary">
                                    الفئة العمرية <span class="text-danger">*</span>
                                </label>
                                <select name="age_category_id" id="age_category_id"
                                        class="form-select form-control-modern @error('age_category_id') is-invalid @enderror"
                                        required>
                                    <option value="">اختر الفئة</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" {{ old('age_category_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('age_category_id')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- اسم الخطة -->
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold text-primary">
                                    اسم الخطة <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name') }}"
                                       class="form-control form-control-modern @error('name') is-invalid @enderror"
                                       placeholder="مثال: خطة صيفية للكبار"
                                       required>
                                @error('name')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- المدة -->
                            <div class="col-md-6">
                                <label for="duration_value" class="form-label fw-semibold text-primary">
                                    المدة (القيمة الرقمية) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       name="duration_value"
                                       id="duration_value"
                                       value="{{ old('duration_value') }}"
                                       min="1"
                                       class="form-control form-control-modern @error('duration_value') is-invalid @enderror"
                                       placeholder="مثال: 3"
                                       required>
                                @error('duration_value')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="duration_unit" class="form-label fw-semibold text-primary">
                                    وحدة المدة
                                </label>
                                <select name="duration_unit" id="duration_unit"
                                        class="form-select form-control-modern @error('duration_unit') is-invalid @enderror">
                                    <option value="day" {{ old('duration_unit') == 'day' ? 'selected' : '' }}>يوم</option>
                                    <option value="week" {{ old('duration_unit') == 'week' ? 'selected' : '' }}>أسبوع</option>
                                    <option value="month" {{ old('duration_unit') == 'month' ? 'selected' : '' }}>شهر</option>
                                    <option value="season" {{ old('duration_unit') == 'season' ? 'selected' : '' }}>موسم</option>
                                </select>
                                @error('duration_unit')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- عدد الحصص في الأسبوع -->
                            <div class="col-md-6">
                                <label for="sessions_per_week" class="form-label fw-semibold text-primary">
                                    عدد الحصص في الأسبوع
                                </label>
                                <input type="number"
                                       name="sessions_per_week"
                                       id="sessions_per_week"
                                       value="{{ old('sessions_per_week') }}"
                                       min="0"
                                       class="form-control form-control-modern @error('sessions_per_week') is-invalid @enderror"
                                       placeholder="مثال: 3">
                                @error('sessions_per_week')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- نوع الاشتراك -->
                            <div class="col-md-6">
                                <label for="pricing_type" class="form-label fw-semibold text-primary">
                                    نوع الاشتراك <span class="text-danger">*</span>
                                </label>
                                <select name="pricing_type" id="pricing_type"
                                        class="form-select form-control-modern @error('pricing_type') is-invalid @enderror"
                                        required>
                                    <option value="session" {{ old('pricing_type') == 'session' ? 'selected' : '' }}>بالحصة</option>
                                    <option value="weekly" {{ old('pricing_type') == 'weekly' ? 'selected' : '' }}>أسبوعيًا</option>
                                    <option value="monthly" {{ old('pricing_type') == 'monthly' ? 'selected' : '' }}>شهريًا</option>
                                    <option value="season" {{ old('pricing_type') == 'season' ? 'selected' : '' }}>موسمي</option>
                                    <option value="ticket" {{ old('pricing_type') == 'ticket' ? 'selected' : '' }}>بطاقة دخول</option>
                                </select>
                                @error('pricing_type')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الجنس -->
                            <div class="col-md-6">
                                <label for="sexe" class="form-label fw-semibold text-primary">
                                    الجنس
                                </label>
                                <select name="sexe" id="sexe"
                                        class="form-select form-control-modern @error('sexe') is-invalid @enderror">
                                    <option value="X" {{ old('sexe') == 'X' ? 'selected' : '' }}>مختلط</option>
                                    <option value="H" {{ old('sexe') == 'H' ? 'selected' : '' }}>ذكور</option>
                                    <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>إناث</option>
                                </select>
                                @error('sexe')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- نوع العميل -->
                            <div class="col-md-6">
                                <label for="type_client" class="form-label fw-semibold text-primary">
                                    نوع العميل
                                </label>
                                <select name="type_client" id="type_client"
                                        class="form-select form-control-modern @error('type_client') is-invalid @enderror">
                                    <option value="person" {{ old('type_client') == 'person' ? 'selected' : '' }}>أفراد</option>
                                    <option value="club" {{ old('type_client') == 'club' ? 'selected' : '' }}>نوادي</option>
                                    <option value="company" {{ old('type_client') == 'company' ? 'selected' : '' }}>شركات</option>
                                </select>
                                @error('type_client')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- السعر -->
                            <div class="col-md-6">
                                <label for="price" class="form-label fw-semibold text-primary">
                                    السعر (دج) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       name="price"
                                       id="price"
                                       value="{{ old('price') }}"
                                       min="0"
                                       step="0.01"
                                       class="form-control form-control-modern @error('price') is-invalid @enderror"
                                       required>
                                @error('price')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- بداية الصلاحية -->
                            <div class="col-md-6">
                                <label for="valid_from" class="form-label fw-semibold text-primary">
                                    بداية الصلاحية
                                </label>
                                <input type="text"
                                       name="valid_from"
                                       id="valid_from"
                                       value="{{ old('valid_from', date('Y-m-d')) }}"
                                       class="form-control js-date-fr form-control-modern @error('valid_from') is-invalid @enderror">
                                @error('valid_from')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- نهاية الصلاحية -->
                            <div class="col-md-6">
                                <label for="valid_to" class="form-label fw-semibold text-primary">
                                    نهاية الصلاحية
                                </label>
                                <input type="text"
                                       name="valid_to"
                                       id="valid_to"
                                       value="{{ old('valid_to') }}"
                                       class="form-control js-date-fr form-control-modern @error('valid_to') is-invalid @enderror">
                                @error('valid_to')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- مفعل؟ -->
                            <div class="col-md-6">
                                <label for="active" class="form-label fw-semibold text-primary">
                                    حالة الخطة
                                </label>
                                <select name="active" id="active"
                                        class="form-select form-control-modern @error('active') is-invalid @enderror">
                                    <option value="1" {{ old('active', 1) == 1 ? 'selected' : '' }}>مفعلة</option>
                                    <option value="0" {{ old('active') == 0 ? 'selected' : '' }}>غير مفعلة</option>
                                </select>
                                @error('active')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <!-- أزرار الحفظ والرجوع -->
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-end mt-5 pt-3">
                            <a href="{{ route('admin.pricing_plans.index') }}"
                               class="btn btn-outline-secondary btn-lg rounded-pill px-5 shadow-sm order-sm-2">
                                رجوع
                            </a>
                            <button type="submit"
                                    class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg btn-glow order-sm-1">
                                💾 حفظ الخطة
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

{{-- ======================== STYLE 2026 (متسق مع الصفحات السابقة) ======================== --}}
<style>
    :root {
        --primary: #4361ee;
        --primary-gradient: linear-gradient(135deg, #4361ee, #4cc9f0);
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

    .bg-gradient-primary {
        background: var(--primary-gradient);
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

    .btn-glow {
        background: var(--primary-gradient);
        color: white;
        transition: all 0.4s ease;
    }

    .btn-glow:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(67, 97, 238, 0.4);
        color: white;
    }

    @media (max-width: 576px) {
        .card-body { padding: 2rem !important; }
        .btn-lg { width: 100%; }
    }
</style>