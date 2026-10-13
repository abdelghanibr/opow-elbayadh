@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction:rtl;text-align:right;max-width:1000px">

<h3 class="fw-bold mb-4">✏️ تعديل ملف النادي</h3>
<div class="alert alert-info py-2 small">
    📎 يُرجى التأكد من أن حجم كل ملف مرفق لا يتجاوز <strong>2 ميغابايت (2MB)</strong>
    وأن صيغته إحدى الصيغ: <strong>PDF, JPG, JPEG, PNG</strong>.
    <hr class="my-2">
    <div class="mb-0">
        ⚠️ <strong>ملاحظة مهمة:</strong>
        يُرفَع كل ملف على حدة. في كل مرة يجب اختيار ملف واحد (أو ملفين كحد أقصى) ثم الضغط على الحفظ، وذلك لأن السعة الإجمالية المسموح بها على الخادم محدودة.
        إذا ظهر خطأ <strong>«تعذّر رفع الملف على الخادم»</strong> فالسبب أن الحجم يتجاوز الحد المسموح به: يُرجى ضغط الملف ثم إعادة المحاولة.
    </div>
</div>

{{-- ================= FORM ================= --}}
<form id="dossierForm"
      action="{{ route('club.dossier.update') }}"
      method="POST"
      enctype="multipart/form-data">
@csrf
@method('PUT')

@php
$files = App\Models\Club::DOSSIER_FILES;

$attachments = $club->attachments;

$maxKb  = App\Models\Club::DOSSIER_MAX_KB;
$mimes   = App\Models\Club::DOSSIER_MIMES;

$maxLabel = (int) ($maxKb / 1024) . ' ميغابايت';
@endphp

<div class="row g-3">
    {{-- ================== النشاط الرياضي للنادي ================== --}}
    <div class="col-md-6">
        <label class="fw-bold mb-1 d-block">🏅 النشاط الرياضي للنادي</label>
        <select name="activity_id" class="form-select @error('activity_id') is-invalid @enderror">
            <option value="">— اختر النشاط —</option>
            @foreach($activities as $activity)
                <option value="{{ $activity->id }}"
                    {{ old('activity_id', $club->activity_id) == $activity->id ? 'selected' : '' }}>
                    {{ $activity->title }}
                </option>
            @endforeach
        </select>
        <div class="form-text">
            نشاط النادي يُعرض تلقائيًا على بطاقات كل أعضائه.
        </div>
        @error('activity_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

@foreach($files as $key => $label)
    <div class="col-md-6">
        <label class="fw-bold mb-1 d-block">{{ $label }}</label>

        <input type="file"
               id="file-{{ $key }}"
               name="{{ $key }}"
               data-label="{{ $label }}"
               accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
               class="form-control @error($key) is-invalid @enderror">

        @error($key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

        {{-- رسالة التحقق في المتصفح --}}
        <div class="small mt-1" id="msg-{{ $key }}" style="display:none"></div>

        {{-- عرض الملف الحالي إن وُجد --}}
        @if(!empty($attachments[$key]))
            <a href="{{ asset($attachments[$key]) }}"
               target="_blank"
               class="btn btn-sm btn-outline-success mt-1">
               👁 عرض الملف الحالي
            </a>
        @endif
    </div>
@endforeach
</div>

{{-- ================= PROGRESS BAR ================= --}}
<div class="mt-4 d-none" id="uploadProgressWrapper">
    <div class="progress" style="height:26px;border-radius:30px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
             role="progressbar"
             style="width:0%"
             id="uploadProgressBar">
            0%
        </div>
    </div>
    <small class="text-muted d-block mt-1" id="uploadStatus">
        ⏳ جارٍ حفظ الملفات…
    </small>
</div>

{{-- ================= ACTIONS ================= --}}
<div class="mt-5 d-flex justify-content-between">
    <a href="{{ route('club.dossier.index') }}"
       class="btn btn-secondary px-4">
       ⬅ رجوع
    </a>

    <button type="submit"
            id="submitBtn"
            class="btn btn-success px-5 fw-bold">
        💾 حفظ وإرسال
    </button>
</div>

</form>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
(function () {
    'use strict';

    var MAX_BYTES  = {{ $maxKb }} * 1024;
    var MAX_LABEL = @json($maxLabel);
    var ALLOWED_MIMES   = ['application/pdf', 'image/jpeg', 'image/png'];
    var ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png'];

    var form   = document.getElementById('dossierForm');
    var fileInputs = Array.prototype.slice.call(form.querySelectorAll('input[type="file"]'));

    var wrapper = document.getElementById('uploadProgressWrapper');
    var bar     = document.getElementById('uploadProgressBar');
    var status  = document.getElementById('uploadStatus');
    var btn     = document.getElementById('submitBtn');

    function megabytes(bytes) {
        return (bytes / 1024 / 1024).toFixed(2);
    }

    function extensionOf(name) {
        return (name.split('.').pop() || '').toLowerCase();
    }

    function clearState(input) {
        input.classList.remove('is-invalid');

        var box = document.getElementById('msg-' + input.name);
        if (box) {
            box.textContent = '';
            box.style.display = 'none';
            box.removeAttribute('data-error');
        }
    }

    function setError(input, message) {
        input.classList.add('is-invalid');

        var box = document.getElementById('msg-' + input.name);
        if (box) {
            box.textContent = message;
            box.style.color = '#dc3545';
            box.style.display = 'block';
            box.setAttribute('data-error', '1');
        }
    }

    function setSuccess(input, message) {
        input.classList.remove('is-invalid');

        var box = document.getElementById('msg-' + input.name);
        if (box) {
            box.textContent = message;
            box.style.color = '#198754';
            box.style.display = 'block';
            box.removeAttribute('data-error');
        }
    }

    /**
     * يتحقق من ملف واحد: الصيغة ثم الحجم.
     * الحقل غير إلزامي، donc absence de fichier = valide.
     */
    function validateFile(input) {
        clearState(input);

        if (!input.files || input.files.length === 0) {
            return true;
        }

        var file = input.files[0];
        var name = input.getAttribute('data-label') || input.name;
        var ext  = extensionOf(file.name);

        if (ALLOWED_MIMES.indexOf(file.type) === -1 && ALLOWED_EXTENSIONS.indexOf(ext) === -1) {
            setError(input,
                '❌ ' + name + ': الصيغة غير مدعومة (' + (file.type || ext || 'غير معروفة') +
                '). الصيغ المسموحة: PDF, JPG, JPEG, PNG.');
            input.value = '';
            return false;
        }

        if (file.size > MAX_BYTES) {
            setError(input,
                '❌ ' + name + ': الحجم ' + megabytes(file.size) + ' ميغابايت يتجاوز الحد المسموح به (' +
                MAX_LABEL + '). يُرجى ضغط الملف أو تقسيمه ثم إعادة المحاولة.');
            input.value = '';
            return false;
        }

        setSuccess(input,
            '✅ ' + file.name + ' (' + megabytes(file.size) + ' ميغابايت)');
        return true;
    }

    // تحقق فوري عند اختيار كل ملف
    fileInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            validateFile(input);
        });
    });

    form.addEventListener('submit', function (e) {
        var valid = true;
        var firstInvalid = null;

        fileInputs.forEach(function (input) {
            if (!validateFile(input)) {
                valid = false;
                if (!firstInvalid) {
                    firstInvalid = input;
                }
            }
        });

        if (!valid) {
            e.preventDefault();

            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
            return;
        }

        e.preventDefault();

        var formData = new FormData(form);

        // إظهار Progress
        wrapper.classList.remove('d-none');
        bar.style.width = '0%';
        bar.textContent = '0%';
        bar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-primary';
        status.textContent = '⏳ جارٍ حفظ الملفات…';

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جارٍ الحفظ…';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);

        // يسمح لـ Laravel بإرجاع أخطاء التحقق بصيغة JSON
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.onprogress = function (ev) {
            if (ev.lengthComputable) {
                var percent = Math.round((ev.loaded / ev.total) * 100);
                bar.style.width = percent + '%';
                bar.textContent = percent + '%';
            }
        };

        function stopProgress() {
            bar.classList.remove('progress-bar-animated');
            btn.disabled = false;
            btn.innerHTML = '💾 حفظ وإرسال';
        }

        function fail(text) {
            stopProgress();
            bar.classList.replace('bg-primary', 'bg-danger');
            bar.textContent = '❌ ' + text;
            status.textContent = text;
        }

        xhr.onload = function () {
            // Laravel répond 302 puis le XHR suit la redirection -> 200.
            if (xhr.status >= 200 && xhr.status < 400) {
                stopProgress();
                bar.classList.replace('bg-primary', 'bg-success');
                bar.style.width = '100%';
                bar.textContent = '✅ تم الحفظ بنجاح';
                status.textContent = '✅ تم الحفظ بنجاح، جارٍ التحويل…';

                setTimeout(function () {
                    window.location.href = @json(route('club.dossier.index'));
                }, 900);
                return;
            }

            var messages = [];

            // 422 = erreurs de validation renvoyées par le serveur
            try {
                var payload = JSON.parse(xhr.responseText);

                if (payload.errors) {
                    Object.keys(payload.errors).forEach(function (field) {
                        messages = messages.concat(payload.errors[field]);
                    });
                } else if (payload.message) {
                    messages.push(payload.message);
                }
            } catch (e) {
                if (xhr.status === 419) {
                    messages.push('انتهت صلاحية الجلسة أو تجاوز الحجم الإجمالي المسموح به على الخادم (post_max_size). يُرجى إعادة تحميل الصفحة ثم اختيار عدد أقل من الملفات.');
                } else if (xhr.status === 413 || xhr.status === 400) {
                    messages.push('الحجم الإجمالي المُرسل يتجاوز الحد المسموح به على الخادم. يُرجى اختيار عدد أقل من الملفات أو ضغطها.');
                } else if (xhr.status >= 500) {
                    messages.push('خطأ تقني في الخادم (' + xhr.status + '). يُرجى إعادة المحاولة بعد قليل.');
                } else if (payloadIsHtml(xhr.responseText)) {
                    messages.push('تعذّر قراءة تفاصيل الخطأ من الخادم (رمز ' + xhr.status + ').');
                }
            }

            if (!messages.length) {
                messages.push('خطأ أثناء الحفظ (رمز ' + xhr.status + ').');
            }

            fail(messages.join(' — '));
        };

        xhr.onerror = function () {
            fail('❌ فشل الاتصال');
        };

        xhr.send(formData);
    });

    function payloadIsHtml(text) {
        return typeof text === 'string' && text.indexOf('<') !== -1;
    }
})();
</script>
@endsection