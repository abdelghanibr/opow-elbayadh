@php
    $birthDate = old('birth_date', $person->birth_date ?? null);
    $attachments = [];
    if (isset($dossier) && $dossier && $dossier->attachments) {
        $attachments = is_array($dossier->attachments)
            ? $dossier->attachments
            : json_decode($dossier->attachments, true);
    }
@endphp

<div class="container py-5">
    <form action="{{ route('profile.step.save', 4) }}"
          method="POST"
          enctype="multipart/form-data"
          id="documentsForm">
        @csrf

        <h3 class="mb-5 text-center fw-bold text-gradient">الوثائق المطلوبة</h3>

@php
    $dossierId = $dossier?->id;

    $formulaireUrl = $dossierId
        ? route('forms.formulaire.view', $dossierId)
        : null;

    $autorisationParentaleUrl = $dossierId
        ? route('dossiers.autorisation-parentale', $dossierId)
        : null;
@endphp

<div class="dash-box mt-4">
    <h4 class="mb-3">📥 تحميل النماذج الرسمية</h4>

    <p class="text-muted mb-3">
        يرجى تحميل النماذج التالية، تعبئتها، ثم إعادة رفعها في ملفك الشخصي.
    </p>

    <div class="row g-3">

        {{-- 📄 نموذج التعهّد --}}
        <div class="col-md-6">
            <div class="dash-card">
                <h6>📄 نموذج التعهّد</h6>
                <p class="text-muted small">
                    خاص بالمشاركين البالغين
                </p>

                @if($formulaireUrl)
                    <button type="button"
                            class="btn btn-success btn-sm js-print-doc"
                            data-url="{{ $formulaireUrl }}">
                        🖨 طباعة النموذج مباشرة
                    </button>
                @else
                    <div class="alert alert-warning mt-2 mb-0 py-2">
                        قم بإدراج معلوماتك الشخصية للتمكن من تحميل الوثائق جاهزة
                    </div>
                @endif
            </div>
        </div>

        {{-- 📄 التصريح الأبوي --}}
        <div class="col-md-6">
            <div class="dash-card">
                <h6>📄 نموذج التصريح الأبوي</h6>
                <p class="text-muted small">
                    خاص بالمشاركين القُصّر
                </p>

                @if($autorisationParentaleUrl)
                    <button type="button"
                            class="btn btn-success btn-sm js-print-doc"
                            data-url="{{ $autorisationParentaleUrl }}">
                        🖨 طباعة النموذج مباشرة
                    </button>
                @else
                    <div class="alert alert-warning mt-2 mb-0 py-2">
                        قم بإدراج معلوماتك الشخصية للتمكن من تحميل الوثائق جاهزة
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<iframe id="printFrame" style="display:none;"></iframe>

        <div class="row g-4 g-lg-5">

            <!-- ================= صورة شمسية ================= -->
            <div class="col-lg-5 col-md-6">
                <label class="form-label fw-bold mb-3 text-primary">📷 صورة شمسية</label>

                <div class="photo-upload-modern text-center position-relative d-inline-block mb-4">
                    <img id="photoPreview"
                         src="{{ isset($attachments['photo'])
                                ? asset($attachments['photo'])
                                : asset('images/avatar-placeholder.png') }}"
                         alt="معاينة الصورة"
                         class="photo-preview-img">

                    <!-- Cercle de progression globale (tous les fichiers) -->
                    <svg class="progress-circle" viewBox="0 0 160 160">
                        <circle class="track" cx="80" cy="80" r="74"/>
                        <circle class="progress" cx="80" cy="80" r="74"/>
                    </svg>

                    <div class="progress-percent">0%</div>
                    <div class="photo-overlay"></div>
                </div>

                @if(isset($attachments['photo']))
                    <div class="text-center mb-3">
                        <a href="{{ asset($attachments['photo']) }}" target="_blank"
                           class="btn btn-outline-success btn-sm rounded-pill px-4 shadow-sm">
                            👁 عرض الصورة الحالية
                        </a>
                    </div>
                @endif

                <div class="alert alert-soft-info rounded-4 p-3">
                    <strong class="d-block mb-2">📌 شروط الصورة:</strong>
                    <ul class="small mb-0 ps-3">
                        <li>خلفية بيضاء نقية</li>
                        <li>وجه واضح بدون نظارات أو غطاء</li>
                        <li>JPG أو PNG • حجم ≤ 2 ميغابايت</li>
                    </ul>
                </div>

                <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png"
                       class="form-control form-control-modern mt-3 @error('photo') is-invalid @enderror">
             
                @error('photo')
    <div class="text-danger small mt-2 fw-bold">
        {{ $message }}
    </div>
@enderror
            </div>

            <!-- ================= الوثائق (PDFs) ================= -->
            <div class="col-lg-7 col-md-6">
                <div class="row g-4">

                    <!-- شهادة طبية -->
                    <div class="col-12">
                        <label class="form-label fw-bold">🩺 شهادة طبية / صدرية
                            @if(isset($attachments['medical_certificate']))
                                <a href="{{ $attachments['medical_certificate'] }}" target="_blank"
                                   class="btn btn-outline-success btn-sm rounded-pill ms-2">👁 عرض</a>
                            @endif
                        </label>
                        <input type="file" name="medical_certificate"
                               class="form-control form-control-modern @error('medical_certificate') is-invalid @enderror">
                        @error('medical_certificate')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        
                        
                         <div class="alert alert-warning mt-2 p-2 small rounded-3">
        <strong>ملاحظة مهمة:</strong>
        <ul class="mt-1 mb-1 ps-3">
            <li>بالنسبة لنشاط السباحة: شهادة طبية صدرية تُثبت عدم الإصابة بأي مرض يمنع ممارسة السباحة.</li> 
            <li>بالنسبة لباقي النشاطات الرياضية، يجب تقديم شهادة طبية تسمح بممارسة النشاط الرياضي.</li> 
            <li>الشهادة يجب أن تكون حديثة وصادرة من طبيب عام أو مختص.</li>
        </ul>
    </div>
                        
                    </div>

                    <!-- تعهد -->
                    <div id="adult-pledge" class="col-12" style="display: none;">
                        <label class="form-label fw-bold">✍️ تعهد
                            @if(isset($attachments['engagement']))
                                <a href="{{ $attachments['engagement'] }}" target="_blank"
                                   class="btn btn-outline-success btn-sm rounded-pill ms-2">👁 عرض</a>
                            @endif
                        </label>
                        <input type="file" name="engagement"
                               class="form-control form-control-modern @error('engagement') is-invalid @enderror">
                        @error('engagement')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- وثائق القاصر -->
                    <div id="minor-docs" class="col-12" style="display: none;">
                        <hr class="my-4 border-warning opacity-25">
                        <h5 class="text-warning fw-bold mb-3">وثائق إضافية (أقل من 18 سنة)</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">📄 شهادة الميلاد @if(isset($attachments['birth_certificate']))<a href="{{ $attachments['birth_certificate'] }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill ms-2">👁 عرض</a>@endif</label>
                                <input type="file" name="birth_certificate" class="form-control form-control-modern @error('birth_certificate') is-invalid @enderror">
                                @error('birth_certificate')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">📝 تصريح أبوي @if(isset($attachments['parental_authorization']))<a href="{{ $attachments['parental_authorization'] }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill ms-2">👁 عرض</a>@endif</label>
                                <input type="file" name="parental_authorization" class="form-control form-control-modern @error('parental_authorization') is-invalid @enderror">
                                @error('parental_authorization')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">🪪 بطاقة الولي @if(isset($attachments['guardian_id_card']))<a href="{{ $attachments['guardian_id_card'] }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill ms-2">👁 عرض</a>@endif</label>
                                <input type="file" name="guardian_id_card" class="form-control form-control-modern @error('guardian_id_card') is-invalid @enderror">
                                @error('guardian_id_card')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- وثائق البالغ -->
                    <div id="adult-docs" class="col-12" style="display: none;">
                        <hr class="my-4 border-info opacity-25">
                        <h5 class="text-info fw-bold mb-3">وثيقة إضافية (18 سنة فأكثر)</h5>
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">🪪 بطاقة التعريف الوطنية
                                    @if(isset($attachments['national_id_card']))
                                        <a href="{{ $attachments['national_id_card'] }}" target="_blank"
                                           class="btn btn-outline-success btn-sm rounded-pill ms-2">👁 عرض</a>
                                    @endif
                                </label>
                                <input type="file" name="national_id_card"
                                       class="form-control form-control-modern @error('national_id_card') is-invalid @enderror">
                                @error('national_id_card')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <input type="hidden" name="birth_date" id="birth_date" value="{{ $birthDate }}">
        </div>

        <div class="d-flex justify-content-between mt-5 pt-4">
            <a href="{{ route('profile.step', 3) }}" class="btn btn-secondary px-4">السابق</a>
            <button type="submit" class="btn btn-success px-4">
                إنهاء التسجيل
            </button>
        </div>
    </form>
</div>

<!-- ======================== CSS MODERNE ======================== -->
<style>
    :root {
        --primary: #4361ee;
        --success: #4cc9f0;
        --glass: rgba(255, 255, 255, 0.18);
        --shadow: 0 8px 32px rgba(0,0,0,0.12);
    }

    body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }

    .text-gradient {
        background: linear-gradient(90deg, var(--primary), var(--success));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-control-modern {
        border: none;
        border-radius: 1rem;
        padding: 0.8rem 1.2rem;
        background: var(--glass);
        backdrop-filter: blur(12px);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
    }

    .form-control-modern:focus {
        transform: translateY(-3px);
        box-shadow: 0 0 0 4px rgba(67,97,238,0.25);
    }

    .alert-soft-info {
        background: linear-gradient(135deg, #e0f7fa 0%, #cffafe 100%);
        border: none;
        backdrop-filter: blur(8px);
    }

    .btn-glow {
        background: linear-gradient(45deg, var(--primary), var(--success));
        border: none;
        color: white;
        transition: all 0.4s ease;
    }

    .btn-glow:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(67,97,238,0.4);
    }

    .photo-preview-img {
        width: 160px; height: 160px;
        object-fit: cover;
        border-radius: 50%;
        border: 6px solid white;
        box-shadow: var(--shadow);
        transition: all 0.5s ease;
    }

    .photo-preview-img:hover { transform: scale(1.08) rotate(3deg); }

    .photo-overlay {
        position: absolute; inset: 0;
        border-radius: 50%;
        background: radial-gradient(circle, transparent 60%, rgba(67,97,238,0.15));
        opacity: 0; transition: opacity 0.4s;
        pointer-events: none;
    }

    .photo-preview-img:hover + .photo-overlay { opacity: 1; }

    .progress-circle {
        position: absolute;
        top: -6px; left: -6px;
        width: 172px; height: 172px;
        transform: rotate(-90deg);
        opacity: 0;
        transition: opacity 0.5s ease;
    }

    .track { stroke: rgba(255,255,255,0.3); stroke-width: 8; fill: none; }
    .progress { stroke: url(#gradient); stroke-width: 8; stroke-linecap: round; fill: none;
                stroke-dasharray: 465; stroke-dashoffset: 465;
                transition: stroke-dashoffset 0.8s ease-in-out; }

    .progress-percent {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.5rem; font-weight: bold;
        color: var(--primary);
        opacity: 0;
        transition: opacity 0.5s ease;
    }

    @media (max-width: 768px) {
        .photo-preview-img, .progress-circle { width: 140px; height: 140px; }
        .progress-circle { width: 152px; height: 152px; }
    }
    .dash-box {
    background: rgba(255, 255, 255, 0.72);
    backdrop-filter: blur(12px);
    border-radius: 1.25rem;
    padding: 1.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
}

.dash-card {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 1rem;
    padding: 1.25rem;
    height: 100%;
    box-shadow: 0 6px 22px rgba(0, 0, 0, 0.06);
    transition: all 0.25s ease;
}

.dash-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.09);
}
</style>

<!-- ======================== GRADIENT SVG ======================== -->
<svg width="0" height="0">
    <defs>
        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#4361ee"/>
            <stop offset="100%" stop-color="#4cc9f0"/>
        </linearGradient>
    </defs>
</svg>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const printFrame = document.getElementById('printFrame');

    document.querySelectorAll('.js-print-doc').forEach(button => {
        button.addEventListener('click', function () {
            const url = this.dataset.url;
            if (!url) return;

            this.disabled = true;
            const oldText = this.innerHTML;
            this.innerHTML = '⏳ جاري التحضير للطباعة...';

            printFrame.onload = function () {
                try {
                    setTimeout(() => {
                        printFrame.contentWindow.focus();
                        printFrame.contentWindow.print();
                        button.disabled = false;
                        button.innerHTML = oldText;
                    }, 500);
                } catch (e) {
                    console.error(e);
                    alert('تعذر فتح نافذة الطباعة.');
                    button.disabled = false;
                    button.innerHTML = oldText;
                }
            };

            printFrame.src = url;
        });
    });
});
</script>
<!-- ======================== JAVASCRIPT - PROGRESSION GLOBALE ======================== -->
<script>


document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('documentsForm');
    const fileInputs = form.querySelectorAll('input[type="file"]');
    const photoInput = document.getElementById('photoInput');
    const previewImg = document.getElementById('photoPreview');

    const progressCircle = document.querySelector('.progress-circle');
    const progressBar = document.querySelector('.progress');
    const progressPercent = document.querySelector('.progress-percent');
    const circumference = 2 * Math.PI * 74;

    progressBar.style.strokeDasharray = circumference;

    // =========================
    // دوال أخطاء
    // =========================
    function showError(input, message) {
        let errorBox = input.parentNode.querySelector('.file-error');
        if (!errorBox) {
            errorBox = document.createElement('div');
            errorBox.className = "file-error text-danger mt-2 fw-bold";
            input.parentNode.appendChild(errorBox);
        }
        errorBox.textContent = message;
        input.classList.add('is-invalid');
    }

    function clearError(input) {
        let errorBox = input.parentNode.querySelector('.file-error');
        if (errorBox) errorBox.remove();
        input.classList.remove('is-invalid');
    }

    // =========================
    // معاينة الصورة + تحقق الحجم والصيغة
    // =========================
    photoInput.addEventListener('change', e => {
        clearError(photoInput);

        if (e.target.files[0]) {
            let file = e.target.files[0];

            if (file.size > 2 * 1024 * 1024) {
                showError(photoInput, "❌ حجم الصورة أكبر من 2 ميغابايت.");
                photoInput.value = "";
                return;
            }

            const allowed = ['image/jpeg', 'image/png'];
            if (!allowed.includes(file.type)) {
                showError(photoInput, "❌ يجب أن تكون الصورة JPG أو PNG.");
                photoInput.value = "";
                return;
            }

            const reader = new FileReader();
            reader.onload = ev => previewImg.src = ev.target.result;
            reader.readAsDataURL(file);
        }
    });

    // =========================
    // إظهار وثائق القاصر / البالغ
    // =========================
    function toggleDocuments() {
        const birth = document.getElementById('birth_date').value;
        if (!birth) return;

        const today = new Date();
        const b = new Date(birth);
        let age = today.getFullYear() - b.getFullYear();
        if (today.getMonth() < b.getMonth() || (today.getMonth() === b.getMonth() && today.getDate() < b.getDate())) {
            age--;
        }

        const isMinor = age < 18;
        document.getElementById('minor-docs').style.display = isMinor ? 'block' : 'none';
        document.getElementById('adult-docs').style.display = isMinor ? 'none' : 'block';
        document.getElementById('adult-pledge').style.display = isMinor ? 'none' : 'block';
    }
    toggleDocuments();

    // =========================
    // SUBMIT — فحص شامل + Progress AJAX
    // =========================
    form.addEventListener('submit', function (e) {
        let blocked = false;

        // فحص الحجم لكل ملف
        fileInputs.forEach(input => {
            clearError(input);

            if (input.files.length > 0) {
                let file = input.files[0];

                if (input.name === 'photo' && file.size > 2 * 1024 * 1024) {
                    showError(input, "❌ حجم الصورة أكبر من 2 ميغابايت.");
                    blocked = true;
                }

                if (input.name !== 'photo' && file.size > 4 * 1024 * 1024) {
                    showError(input, "❌ حجم الملف أكبر من 4 ميغابايت.");
                    blocked = true;
                }
            }
        });

        if (blocked) {
            e.preventDefault();
            return;
        }

        // هل يوجد ملف لرفع AJAX؟
        const hasFile = Array.from(fileInputs).some(f => f.files.length > 0);
        if (!hasFile) return; // إرسال عادي

        // رفع عبر AJAX
        e.preventDefault();

        const xhr = new XMLHttpRequest();
        const formData = new FormData(form);

        progressCircle.style.opacity = 1;
        progressPercent.style.opacity = 1;
        progressPercent.textContent = '0%';
        progressBar.style.strokeDashoffset = circumference;

        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);

        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                const percent = Math.round((event.loaded / event.total) * 100);
                progressBar.style.strokeDashoffset = circumference - (percent / 100) * circumference;
                progressPercent.textContent = percent + '%';
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200 || xhr.status === 302) {
                progressPercent.textContent = '✔';
                setTimeout(() => window.location.href = xhr.responseURL || window.location.href, 800);
            } else {
                progressPercent.textContent = '✖';
            }
        };

        xhr.onerror = function() {
            progressPercent.textContent = '✖';
        };

        xhr.send(formData);
    });

});
</script>



