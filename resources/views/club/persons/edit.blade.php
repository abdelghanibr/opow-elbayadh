@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; text-align:right; max-width: 900px;">

    <div class="card shadow-lg border-0 rounded-4">

        <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">✏️ تعديل بيانات المستخدم</h5>
            <span class="fs-5">👤</span>
        </div>

        <div class="card-body p-4">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>⚠ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('club.persons.update', $person->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="editForm">
                @csrf

                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="form-label fw-bold">الاسم</label>
                        <input type="text" name="firstname"
                               class="form-control form-control-lg rounded-3 @error('firstname') is-invalid @enderror"
                               value="{{ old('firstname', $person->firstname) }}"
                               required>
                        @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">اللقب</label>
                        <input type="text" name="lastname"
                               class="form-control form-control-lg rounded-3 @error('lastname') is-invalid @enderror"
                               value="{{ old('lastname', $person->lastname) }}"
                               required>
                        @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">تاريخ الميلاد</label>
                        <input type="text" name="birth_date"
                               class="form-control js-date-fr form-control-lg rounded-3 @error('birth_date') is-invalid @enderror"
                               value="{{ old('birth_date', $person->birth_date) }}"
                               required>
                        @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">رقم الإجازة</label>
                        <input type="text" name="license_number"
                               class="form-control form-control-lg rounded-3 @error('license_number') is-invalid @enderror"
                               value="{{ old('license_number', $person->license_number) }}"
                               required>
                        @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">الجنس</label>
                        <select name="gender"
                                class="form-select form-select-lg rounded-3 @error('gender') is-invalid @enderror" required>
                            <option value="">— اختر —</option>
                            <option value="ذكر" {{ old('gender', $person->gender)=='ذكر'?'selected':'' }}>ذكر</option>
                            <option value="أنثى" {{ old('gender', $person->gender)=='أنثى'?'selected':'' }}>أنثى</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">صفة اللاعب</label>
                        <select name="education"
                                class="form-select form-select-lg rounded-3 @error('education') is-invalid @enderror" required>
                            <option value="">— اختر —</option>
                            @foreach(['لاعب','مدرب','مسير','آخر'] as $role)
                                <option value="{{ $role }}"
                                    {{ old('education', $person->education)==$role?'selected':'' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                        @error('education')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

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
                                    {{ old('study_level', $person->study_level)==$cat?'selected':'' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- ================= صورة شمسية ================= -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">📷 الصورة الشمسية</label>

                        <div class="text-center position-relative d-inline-block mb-3">
                            <img id="photoPreview"
                                 src="{{ $person->photo ? asset($person->photo) : asset('images/avatar-placeholder.png') }}"
                                 alt="معاينة الصورة"
                                 class="rounded-circle shadow-sm"
                                 style="width:130px;height:130px;object-fit:cover;">

                            <svg id="photoProgressCircle" class="progress-circle" viewBox="0 0 160 160" style="display:none;">
                                <circle class="track" cx="80" cy="80" r="74"/>
                                <circle class="progress" cx="80" cy="80" r="74"/>
                            </svg>
                            <div id="photoProgressPercent" class="progress-percent">0%</div>
                        </div>

                        @if($person->photo)
                            <div class="text-center mb-3">
                                <a href="{{ asset($person->photo) }}" target="_blank"
                                   class="btn btn-outline-success btn-sm rounded-pill px-4 shadow-sm">
                                    👁 عرض الصورة الحالية
                                </a>
                            </div>
                        @endif

                        <input type="file" name="photo" id="photoInput"
                               accept="image/jpeg,image/png"
                               class="form-control form-control-lg rounded-3 @error('photo') is-invalid @enderror">
                        @error('photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                        <div id="photoMsg" class="text-center mt-1 fs-6"></div>

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

                    <!-- ================= إستمارة اللاعب PDF ================= -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">📄 إستمارة اللاعب (PDF)</label>

                        @if(!empty($person->birth_certificate))
                            <div class="mb-2 d-flex align-items-center gap-2 flex-wrap">
                                <a href="{{ $person->birth_certificate }}" target="_blank"
                                   class="btn btn-outline-primary btn-sm rounded-pill">
                                    📥 تحميل الملف
                                </a>
                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm rounded-pill"
                                        data-bs-toggle="modal"
                                        data-bs-target="#pdfPreviewModal">
                                    👁 معاينة
                                </button>
                            </div>

                            <div class="pdf-inline-preview mt-2">
                                <iframe src="{{ $person->birth_certificate }}"
                                        style="width:100%;height:300px;border:1px solid #dee2e6;border-radius:12px;"
                                        title="معاينة الإستمارة">
                                </iframe>
                            </div>
                        @else
                            <div class="alert alert-info py-2 px-3 mb-2" style="border-radius:12px;font-size:14px;">
                                ⚠ لم يتم رفع إستمارة بعد
                            </div>
                        @endif

                        <input type="file"
                               name="registration_form_pdf"
                               id="pdfInput"
                               class="form-control form-control-lg rounded-3 mt-2 @error('registration_form_pdf') is-invalid @enderror"
                               accept="application/pdf">
                        @error('registration_form_pdf')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                        <div id="pdfName" class="mt-2 text-muted fs-6"></div>

                        <div id="newPdfPreview" class="mt-3" style="display:none;">
                            <div class="text-center position-relative d-inline-block mb-2">
                                <svg id="pdfProgressCircle" class="progress-circle" viewBox="0 0 160 160" style="display:none;">
                                    <circle class="track" cx="80" cy="80" r="74"/>
                                    <circle class="progress" cx="80" cy="80" r="74"/>
                                </svg>
                                <div id="pdfProgressPercent" class="progress-percent">0%</div>
                            </div>
                            <iframe id="newPdfFrame"
                                    style="width:100%;height:300px;border:1px solid #dee2e6;border-radius:12px;"
                                    title="معاينة الإستمارة"></iframe>
                            <div class="mt-2 d-flex gap-2">
                                <a id="newPdfLink" href="#" target="_blank"
                                   class="btn btn-outline-primary btn-sm rounded-pill">📥 تحميل</a>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#newPdfModal">👁 معاينة</button>
                            </div>
                        </div>

                        <div class="pdf-rules mt-3">
                            <div class="rules-title">⭐ شروط الملف</div>
                            <ul class="rules-list">
                                <li>الصيغة PDF فقط</li>
                                <li>الحجم أقل من 3MB</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="text-center mt-5">
                    <button type="submit"
                            id="submitBtn"
                            class="btn btn-success btn-lg px-5 rounded-pill shadow">
                        💾 حفظ التعديلات
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@if(!empty($person->birth_certificate))
<div class="modal fade" id="pdfPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📄 معاينة الإستمارة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe src="{{ $person->birth_certificate }}"
                        style="width:100%;height:100%;border:none;"
                        title="معاينة الإستمارة">
                </iframe>
            </div>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="newPdfModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📄 معاينة الإستمارة الجديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="newPdfModalFrame"
                        style="width:100%;height:100%;border:none;"
                        title="معاينة الإستمارة">
                </iframe>
            </div>
        </div>
    </div>
</div>

<style>
.photo-rules, .pdf-rules {
    border-radius: 14px;
    padding: 14px 18px;
    font-size: 14px;
}
.photo-rules {
    background: #eaf6ff;
    border: 1px solid #b6e1ff;
}
.pdf-rules {
    background: #fff3e6;
    border: 1px solid #ffc98c;
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

.card {
    animation: fadeUp .5s ease-in-out;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Progress circle (from step4 pattern) */
.progress-circle {
    position: absolute;
    top: -5px; left: 50%;
    transform: translateX(-50%) rotate(-90deg);
    width: 142px; height: 142px;
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}
.track { stroke: rgba(255,255,255,0.3); stroke-width: 8; fill: none; }
.progress {
    stroke: url(#progressGradient);
    stroke-width: 8;
    stroke-linecap: round;
    fill: none;
    stroke-dasharray: 465;
    stroke-dashoffset: 465;
    transition: stroke-dashoffset 0.6s ease-in-out;
}
.progress-percent {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    font-size: 1.3rem;
    font-weight: bold;
    color: #198754;
    opacity: 0;
    transition: opacity 0.4s;
    pointer-events: none;
}
</style>

<svg width="0" height="0">
    <defs>
        <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#198754"/>
            <stop offset="100%" stop-color="#23d36b"/>
        </linearGradient>
    </defs>
</svg>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('editForm');
    var circumference = 2 * Math.PI * 74;

    function clearFieldError(el) {
        el.classList.remove('is-invalid');
        var fb = el.parentNode.querySelector('.invalid-feedback');
        if (fb) fb.remove();
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

    function showProgress(id, show) {
        var circle = document.getElementById(id + 'ProgressCircle');
        var pct = document.getElementById(id + 'ProgressPercent');
        if (!circle || !pct) return;
        if (show) {
            circle.style.opacity = '1';
            pct.style.opacity = '1';
            pct.textContent = '0%';
            var p = circle.querySelector('.progress');
            p.style.strokeDasharray = circumference;
            p.style.strokeDashoffset = circumference;
        } else {
            circle.style.opacity = '0';
            pct.style.opacity = '0';
        }
    }

    function updateProgress(id, percent) {
        var circle = document.getElementById(id + 'ProgressCircle');
        var pct = document.getElementById(id + 'ProgressPercent');
        if (!circle || !pct) return;
        var p = circle.querySelector('.progress');
        p.style.strokeDashoffset = circumference - (percent / 100) * circumference;
        pct.textContent = percent + '%';
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
        if (!file) {
            document.getElementById('newPdfPreview').style.display = 'none';
            return true;
        }
        if (file.type !== 'application/pdf') {
            setFieldError(fileInput, 'يجب أن يكون ملف PDF');
            nameEl.textContent = '❌ الملف يجب أن يكون PDF';
            nameEl.style.color = '#dc3545';
            fileInput.value = '';
            document.getElementById('newPdfPreview').style.display = 'none';
            return false;
        }
        if (file.size > 3 * 1024 * 1024) {
            setFieldError(fileInput, 'الحجم يتجاوز 3MB');
            nameEl.textContent = '❌ الحجم يتجاوز 3MB';
            nameEl.style.color = '#dc3545';
            fileInput.value = '';
            document.getElementById('newPdfPreview').style.display = 'none';
            return false;
        }
        nameEl.textContent = '✅ ' + file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
        nameEl.style.color = '#198754';

        var url = URL.createObjectURL(file);
        document.getElementById('newPdfFrame').src = url;
        document.getElementById('newPdfModalFrame').src = url;
        document.getElementById('newPdfLink').href = url;
        document.getElementById('newPdfPreview').style.display = 'block';
        return true;
    }

    document.getElementById('photoInput').addEventListener('change', function () { validatePhoto(this); });
    document.getElementById('pdfInput').addEventListener('change', function () { validatePDF(this); });

    form.addEventListener('submit', function (e) {
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
            return;
        }

        var hasFile = document.getElementById('photoInput').files.length > 0
                   || document.getElementById('pdfInput').files.length > 0;
        if (!hasFile) return;

        e.preventDefault();

        var xhr = new XMLHttpRequest();
        var formData = new FormData(form);

        var photoCircle = document.getElementById('photoProgressCircle');
        var pdfCircle = document.getElementById('pdfProgressCircle');

        if (document.getElementById('photoInput').files.length > 0) {
            showProgress('photo', true);
        }
        if (document.getElementById('pdfInput').files.length > 0) {
            showProgress('pdf', true);
        }

        var submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ جاري الحفظ...';

        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);

        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                var percent = Math.round((event.loaded / event.total) * 100);
                if (photoCircle && photoCircle.style.opacity === '1') updateProgress('photo', percent);
                if (pdfCircle && pdfCircle.style.opacity === '1') updateProgress('pdf', percent);
            }
        };

        xhr.onload = function () {
            if (xhr.status === 200 || xhr.status === 302) {
                if (photoCircle) updateProgress('photo', 100);
                if (pdfCircle) updateProgress('pdf', 100);
                var pctEl = document.querySelector('.progress-percent[style*="opacity: 1"]');
                setTimeout(function () {
                    window.location.href = xhr.responseURL || window.location.href;
                }, 600);
            } else {
                showProgress('photo', false);
                showProgress('pdf', false);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '💾 حفظ التعديلات';
                if (xhr.responseText) {
                    try {
                        var resp = JSON.parse(xhr.responseText);
                        if (resp.errors) {
                            var html = '<ul class="mb-0">';
                            Object.values(resp.errors).forEach(function (errs) {
                                errs.forEach(function (m) { html += '<li>⚠ ' + m + '</li>'; });
                            });
                            html += '</ul>';
                            var alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-danger';
                            alertDiv.innerHTML = html;
                            form.parentNode.insertBefore(alertDiv, form);
                            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } catch (ex) {}
                }
            }
        };

        xhr.onerror = function () {
            showProgress('photo', false);
            showProgress('pdf', false);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '💾 حفظ التعديلات';
        };

        xhr.send(formData);
    });
});
</script>

@endsection
