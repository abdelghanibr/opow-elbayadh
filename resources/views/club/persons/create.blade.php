@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; text-align:right; max-width: 900px;">

    {{-- 🟦 Card --}}
    <div class="card shadow-lg border-0 rounded-4">

        {{-- Header --}}
        <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">➕ إضافة مستخدم جديد</h5>
            <span class="fs-5">👤</span>
        </div>

        <div class="card-body p-4">

            {{-- أخطاء التحقق --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>⚠ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('club.persons.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="row g-4">

                    {{-- الاسم --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الاسم</label>
                        <input type="text" name="firstname"
                               class="form-control form-control-lg rounded-3"
                               placeholder="أدخل الاسم"
                               value="{{ old('firstname') }}" required>
                    </div>

                    {{-- اللقب --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">اللقب</label>
                        <input type="text" name="lastname"
                               class="form-control form-control-lg rounded-3"
                               placeholder="أدخل اللقب"
                               value="{{ old('lastname') }}" required>
                    </div>

                    {{-- تاريخ الميلاد --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">تاريخ الميلاد</label>
                        <input type="text" name="birth_date"
                               class="form-control js-date-fr form-control-lg rounded-3"
                               value="{{ old('birth_date') }}" required>
                    </div>
                    
                    {{-- رقم الإجازة --}}
<div class="col-md-6">
    <label class="form-label fw-bold">رقم الإجازة</label>
    <input type="text" name="license_number"
           class="form-control form-control-lg rounded-3"
           value="{{ old('license_number') }}"
           required>
</div>


                    {{-- الجنس --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الجنس</label>
                        <select name="gender"
                                class="form-select form-select-lg rounded-3" required>
                            <option value="">— اختر —</option>
                            <option value="ذكر" {{ old('gender')=='ذكر'?'selected':'' }}>ذكر</option>
                            <option value="أنثى" {{ old('gender')=='أنثى'?'selected':'' }}>أنثى</option>
                        </select>
                    </div>

                    {{-- صفة اللاعب --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">صفة اللاعب</label>
                        <select name="education"
                                class="form-select form-select-lg rounded-3" required>
                            <option value="">— اختر —</option>
                            @foreach(['لاعب','مدرب','مسير','آخر'] as $role)
                                <option value="{{ $role }}" {{ old('education')==$role?'selected':'' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- التصنيف --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">التصنيف</label>
                        <select name="study_level"
                                class="form-select form-select-lg rounded-3">
                            <option value="">— اختر التصنيف —</option>
                            @foreach([
                                'فئة المدارس او البراعم',
                                'فئة اقل من 13 سنة (U13)',
                                'فئة اقل من 16 سنة (U16)',
                                'فئة اقل من 20 سنة (U20)',
                                'فئة الاكابر'
                            ] as $cat)
                                <option value="{{ $cat }}"
                                    {{ old('study_level')==$cat?'selected':'' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- الصورة --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">📷 صورة شمسية</label>

                        <input type="file"
                               name="photo"
                               id="photoInput"
                               class="form-control form-control-lg rounded-3"
                               accept="image/jpeg,image/png"
                               required>

                        {{-- Preview --}}
                        <div class="text-center mt-3">
                            <img id="photoPreview"
                                 src="{{ asset('images/avatar-placeholder.png') }}"
                                 class="rounded-circle shadow-sm"
                                 style="width:120px;height:120px;object-fit:cover;">
                        </div>
                        <div id="photoMsg" class="text-center mt-1 fs-6"></div>

                        {{-- شروط الصورة --}}
                        <div class="photo-rules mt-3">
                            <div class="rules-title">⭐ شروط الصورة</div>
                            <ul class="rules-list">
                                <li>خلفية بيضاء</li>
                                <li>الصيغة JPG أو PNG</li>
                                <li>الحجم أقل من 2MB</li>
                                <li>صورة حديثة (≤ 6 أشهر)</li>
                            </ul>
                        </div>
                    </div>

                    {{-- إستمارة اللاعب PDF --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">📄 إستمارة اللاعب (PDF)</label>

                        <input type="file"
                               name="registration_form_pdf"
                               id="pdfInput"
                               class="form-control form-control-lg rounded-3"
                               accept="application/pdf">

                        <div id="pdfName" class="mt-2 text-muted fs-6"></div>

                        <div class="pdf-rules mt-3">
                            <div class="rules-title">⭐ شروط الملف</div>
                            <ul class="rules-list">
                                <li>الصيغة PDF فقط</li>
                                <li>الحجم أقل من 3MB</li>
                            </ul>
                        </div>
                    </div>

                </div>

                {{-- زر الحفظ --}}
                <div class="text-center mt-5">
                    <button type="submit"
                            class="btn btn-success btn-lg px-5 rounded-pill shadow">
                        💾 حفظ البيانات
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- ================= CSS ================= --}}
<style>
.photo-rules {
    background: #eaf6ff;
    border: 1px solid #b6e1ff;
    border-radius: 14px;
    padding: 14px 18px;
    font-size: 14px;
}

.rules-title {
    color: #0d6efd;
    font-weight: 800;
    margin-bottom: 8px;
}

.rules-list {
    margin: 0;
    padding-right: 18px;
}

.rules-list li {
    color: #084298;
    line-height: 1.9;
}

.pdf-rules {
    background: #fff3e6;
    border: 1px solid #ffc98c;
    border-radius: 14px;
    padding: 14px 18px;
    font-size: 14px;
}

.card {
    animation: fadeUp .5s ease-in-out;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

{{-- ================= JS (Vérification + Preview) ================= --}}
<script>
function clearFieldError(el) {
    el.classList.remove('is-invalid');
    var old = el.parentNode.querySelector('.invalid-feedback');
    if (old) old.remove();
}
function setFieldError(el, msg) {
    el.classList.add('is-invalid');
    if (!el.parentNode.querySelector('.invalid-feedback')) {
        var div = document.createElement('div');
        div.className = 'invalid-feedback d-block';
        div.style.fontSize = '13px';
        div.style.marginTop = '4px';
        div.textContent = msg;
        el.parentNode.appendChild(div);
    }
}

function validateName(field) {
    clearFieldError(field);
    var v = field.value.trim();
    if (!v) { setFieldError(field, 'هذا الحقل مطلوب'); return false; }
    if (v.length < 2) { setFieldError(field, '2 أحرف على الأقل'); return false; }
    if (!/^[A-Za-z\u0600-\u06FF\u0750-\u077F\uFB50-\uFDFF\uFE70-\uFEFF\s'-]+$/.test(v)) {
        setFieldError(field, 'حرف غير مسموح'); return false;
    }
    return true;
}

function validateBirthDate(field) {
    clearFieldError(field);
    var v = field.value.trim();
    if (!v) { setFieldError(field, 'تاريخ الميلاد مطلوب'); return false; }
    var parts;
    if (v.indexOf('/') !== -1) {
        parts = v.split('/');
        var d = parseInt(parts[0]), m = parseInt(parts[1]), y = parseInt(parts[2]);
    } else if (v.indexOf('-') !== -1) {
        parts = v.split('-');
        var y = parseInt(parts[0]), m = parseInt(parts[1]), d = parseInt(parts[2]);
    } else {
        setFieldError(field, 'تاريخ غير صالح'); return false;
    }
    if (parts.length !== 3 || isNaN(d) || isNaN(m) || isNaN(y)) {
        setFieldError(field, 'تاريخ غير صالح'); return false;
    }
    if (m < 1 || m > 12 || d < 1 || d > 31 || y < 1900) {
        setFieldError(field, 'تاريخ غير صالح'); return false;
    }
    var date = new Date(y, m - 1, d);
    var minDate = new Date();
    minDate.setFullYear(minDate.getFullYear() - 3);
    if (date > minDate) { setFieldError(field, 'يجب أن يكون عمر الشخص 3 سنوات على الأقل'); return false; }
    return true;
}

function validateSelect(field) {
    clearFieldError(field);
    if (!field.value) { setFieldError(field, 'الرجاء الاختيار'); return false; }
    return true;
}

function validateText(field, required, maxLen) {
    clearFieldError(field);
    var v = field.value.trim();
    if (required && !v) { setFieldError(field, 'هذا الحقل مطلوب'); return false; }
    if (v && maxLen && v.length > maxLen) { setFieldError(field, 'الحد الأقصى ' + maxLen + ' حرف'); return false; }
    return true;
}

function validatePhoto(fileInput) {
    var msg = document.getElementById('photoMsg');
    clearFieldError(fileInput);
    msg.textContent = '';
    var file = fileInput.files[0];
    if (!file) return true;
    var allowed = ['image/jpeg', 'image/png'];
    if (!allowed.includes(file.type)) {
        setFieldError(fileInput, 'يجب أن تكون JPG أو PNG');
        msg.textContent = '❌ الصورة يجب أن تكون JPG أو PNG';
        msg.style.color = '#dc3545';
        fileInput.value = '';
        return false;
    }
    if (file.size > 2 * 1024 * 1024) {
        setFieldError(fileInput, 'الحجم يتجاوز 2MB');
        msg.textContent = '❌ الحجم يتجاوز 2MB';
        msg.style.color = '#dc3545';
        fileInput.value = '';
        return false;
    }
    msg.textContent = '✅ ' + file.name;
    msg.style.color = '#198754';
    var reader = new FileReader();
    reader.onload = function (ev) {
        document.getElementById('photoPreview').src = ev.target.result;
    };
    reader.readAsDataURL(file);
    return true;
}

function validatePDF(fileInput) {
    var nameEl = document.getElementById('pdfName');
    clearFieldError(fileInput);
    nameEl.textContent = '';
    var file = fileInput.files[0];
    if (!file) return true;
    if (file.type !== 'application/pdf') {
        setFieldError(fileInput, 'يجب أن يكون ملف PDF');
        nameEl.textContent = '❌ الملف يجب أن يكون PDF';
        nameEl.style.color = '#dc3545';
        fileInput.value = '';
        return false;
    }
    if (file.size > 3 * 1024 * 1024) {
        setFieldError(fileInput, 'الحجم يتجاوز 3MB');
        nameEl.textContent = '❌ الحجم يتجاوز 3MB';
        nameEl.style.color = '#dc3545';
        fileInput.value = '';
        return false;
    }
    nameEl.textContent = '✅ ' + file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
    nameEl.style.color = '#198754';
    return true;
}

document.getElementById('photoInput').addEventListener('change', function () { validatePhoto(this); });
document.getElementById('pdfInput').addEventListener('change', function () { validatePDF(this); });

document.querySelector('form').addEventListener('submit', function (e) {
    var ok = true;

    if (!validateName(document.querySelector('[name="firstname"]'))) ok = false;
    if (!validateName(document.querySelector('[name="lastname"]'))) ok = false;
    if (!validateBirthDate(document.querySelector('[name="birth_date"]'))) ok = false;
    if (!validateText(document.querySelector('[name="license_number"]'), true, 50)) ok = false;
    if (!validateSelect(document.querySelector('[name="gender"]'))) ok = false;
    if (!validateSelect(document.querySelector('[name="education"]'))) ok = false;
    if (!validatePhoto(document.getElementById('photoInput'))) ok = false;
    if (!validatePDF(document.getElementById('pdfInput'))) ok = false;

    if (!ok) {
        e.preventDefault();
        var first = document.querySelector('.is-invalid');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endsection
