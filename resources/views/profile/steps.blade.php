@extends('layouts.app')

@section('content')
<style>
/* ======== Modern Stepper 2025 ======== */
.profile-progress {
    width: 100%;
    height: 7px;
    background: #e5e7eb;
    border-radius: 50px;
    margin-bottom: 30px;
    position: relative;
}
.profile-progress-bar {
    background: linear-gradient(90deg, #198754, #23d36b);
    height: 7px;
    width: {{ ($step / 4) * 100 }}%;
    border-radius: 50px;
    transition: .4s ease;
}

.stepper-wrapper {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 10px;
}
.stepper-item {
    text-align: center;
    flex-shrink: 0;
    width: 110px;
}
.step-counter {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    margin: auto;
    font-size: 20px;
    font-weight: bold;
    background: #cfd1d4;
    color: #444;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: .3s;
}
.step-name {
    font-size: 13px;
    font-weight: 600;
    margin-top: 8px;
    white-space: nowrap;
    color: #6c757d;
}

.stepper-item.active .step-counter {
    background: #198754;
    color: #fff;
    transform: scale(1.07);
}
.stepper-item.active .step-name {
    color: #198754;
}
.stepper-item.completed .step-counter {
    background: #28a745;
    color: #fff;
}
.stepper-item.completed .step-name {
    color: #28a745;
}

/* 🔹 لإخفاء Scroll على الحاسوب */
.stepper-wrapper::-webkit-scrollbar {
    height: 5px;
}
.stepper-wrapper::-webkit-scrollbar-thumb {
    background: #198754;
    border-radius: 4px;
}

.box-area {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 15px rgba(0,0,0,.07);
}
</style>


<div class="container py-4" style="direction: rtl; text-align:right;">

    <!-- Progress -->
    <div class="profile-progress">
        <div class="profile-progress-bar"></div>
    </div>

    <!-- Stepper -->
    <div class="stepper-wrapper">
        @for ($i = 1; $i <= 4; $i++)
            <div class="stepper-item 
                {{ $step == $i ? 'active' : '' }}
                {{ $step > $i ? 'completed' : '' }}
            ">
                <div class="step-counter">
                    @if($i == 1) <i class="fa-solid fa-user"></i>
                    @elseif($i == 2) <i class="fa-solid fa-user-shield"></i>
                    @elseif($i == 3) <i class="fa-solid fa-info-circle"></i>
                    @elseif($i == 4) <i class="fa-solid fa-file-medical"></i>
                    @endif
                </div>
                <div class="step-name">
                    @if($i == 1) المعلومات الأساسية
                    @elseif($i == 2) معلومات الولي
                    @elseif($i == 3) معلومات إضافية
                    @elseif($i == 4) الوثائق المطلوبة
                    @endif
                </div>
            </div>
        @endfor
    </div>

    <!-- Errors -->
    @if ($errors->any())
        <div class="alert alert-danger text-right">
            <i class="fa-solid fa-circle-exclamation"></i>
            <strong>⚠ يرجى ملء كل الحقول المطلوبة بشكل صحيح.</strong>
            <ul class="mb-0 mt-1 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Step Content -->
    <div class="box-area">
        @include('profile.steps_parts.step' . $step)
    </div>

</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.box-area form');
    if (!form) return;

    // 🚫 Données anti-doublon des enfants déjà inscrits (même parent)
    var EXISTING_CHILDREN = @json($existingChildren ?? []);
    var CURRENT_PERSON_ID = {{ (int) ($person->id ?? 0) }};
    var IS_CHILD = {{ $isChild ? 'true' : 'false' }};

    // Règles de validation par champ (libellé arabe + message)
    var RULES = {
        'firstname':       { msg: '⚠ الاسم مطلوب' },
        'lastname':        { msg: '⚠ اللقب مطلوب' },
        'birth_date':      { msg: '⚠ تاريخ الميلاد مطلوب' },
        'gender':          { msg: '⚠ يرجى اختيار الجنس' },
        'handicap':        { msg: '⚠ يرجى الإجابة على السؤال' },
        'parent_firstname':{ msg: '⚠ اسم الولي مطلوب' },
        'parent_lastname': { msg: '⚠ لقب الولي مطلوب' },
        'parent_phone':    { msg: '⚠ رقم هاتف الولي مطلوب' },
        'phone':           { msg: '⚠ رقم الهاتف مطلوب' },
        'address':         { msg: '⚠ العنوان مطلوب' },
        'education':       { msg: '⚠ يرجى اختيار الفئة' }
    };

    function groupOf(el) {
        return el.closest('.mb-3') || el.closest('.col-lg-5') || el.closest('.col-12');
    }

    function clearField(el) {
        var group = groupOf(el);
        if (!group) return;
        group.classList.remove('has-missing');
        var hint = group.querySelector('.js-field-hint');
        if (hint) hint.remove();
        if (el.classList) el.classList.remove('is-invalid');
        var g2 = group.querySelectorAll('input');
        if (g2.length <= 1) { el.classList.remove('is-valid'); }
    }

    // Valide UN champ, retourne true/false
    function validateField(el) {
        var name = el.name;
        if (!RULES[name]) return true;
        if (el.type === 'radio') {
            var group = form.querySelectorAll('input[name="' + name + '"]');
            var checked = Array.prototype.some.call(group, function (r) { return r.checked; });
            group.forEach(function (r) {
                clearField(r);
                if (!checked) {
                    var g = groupOf(r);
                    g.classList.add('has-missing');
                    g.querySelectorAll('input').forEach(function (x) { x.classList.add('is-invalid'); });
                    addHint(g, RULES[name].msg);
                }
            });
            return checked;
        }
        if (el.type === 'hidden' || el.type === 'file') return true;

        var val = (el.value || '').trim();
        var ok = val !== '';
        clearField(el);
        if (!ok) {
            var g = groupOf(el);
            if (g) {
                g.classList.add('has-missing');
                el.classList.add('is-invalid');
                addHint(g, RULES[name].msg);
            }
        } else {
            el.classList.add('is-valid');
        }
        return ok;
    }

    function addHint(group, message) {
        var hint = group.querySelector('.js-field-hint');
        if (!hint) {
            hint = document.createElement('div');
            hint.className = 'text-danger small fw-bold mt-1 js-field-hint';
            group.appendChild(hint);
        }
        hint.textContent = message;
    }

    // ==================== Vérifications supplémentaires ====================
    // 🔞 Date de naissance : format valide + âge >= 3 ans (+ < 18 si enfant)
    function validateBirthDate() {
        var el = form.querySelector('input[name="birth_date"]');
        if (!el) return true;

        var val = (el.value || '').trim();
        var group = groupOf(el);
        var msg = null;

        if (val) {
            var m = val.split('-');
            var d = new Date(m[0], m[1] - 1, m[2]);
            var isValid = /^\d{4}-\d{2}-\d{2}$/.test(val) &&
                d.getFullYear() == m[0] && d.getMonth() == m[1] - 1 && d.getDate() == m[2];

            if (!isValid) {
                msg = '⚠ تاريخ الميلاد غير صحيح';
            } else {
                var today = new Date();
                var birth = new Date(m[0], m[1] - 1, m[2]);
                var age = today.getFullYear() - birth.getFullYear();
                var monthDiff = today.getMonth() - birth.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) age--;

                if (age < 3) {
                    msg = '⚠ السن يجب أن يكون 3 سنوات على الأقل';
                }
            }
        }

        clearField(el);
        if (msg) {
            if (group) {
                group.classList.add('has-missing');
                el.classList.add('is-invalid');
                addHint(group, msg);
            }
            return false;
        }
        el.classList.add('is-valid');
        return true;
    }

    // 👶 Anti-doublon : le même enfant ne peut pas être enregistré deux fois
    function validateNoDuplicate() {
        if (!IS_CHILD || !EXISTING_CHILDREN.length) return true;

        var fn = ((form.querySelector('input[name="firstname"]') || {}).value || '').trim().toLowerCase();
        var ln = ((form.querySelector('input[name="lastname"]') || {}).value || '').trim().toLowerCase();
        var bd = ((form.querySelector('input[name="birth_date"]') || {}).value || '').trim();

        if (!fn || !ln || !bd) return true;

        var dup = EXISTING_CHILDREN.find(function (c) {
            return c.id != CURRENT_PERSON_ID &&
                (c.firstname || '').toLowerCase() === fn &&
                (c.lastname || '').toLowerCase() === ln &&
                (c.birth_date || '') === bd;
        });

        if (!dup) return true;

        var group = groupOf(form.querySelector('input[name="firstname"]'));
        if (group) {
            group.classList.add('has-missing');
            form.querySelector('input[name="firstname"]').classList.add('is-invalid');
            addHint(group, '⚠ هذا الطفل مسجل بالفعل على حسابك. لا يمكن تسجيله مرتين.');
        }
        return false;
    }

    // ✅ Validation en temps réel : dès qu'on quitte un champ (blur)
    form.addEventListener('blur', function (e) {
        var el = e.target;
        if (el.name && RULES[el.name]) validateField(el);
        if (el.name === 'birth_date') validateBirthDate();
        if (el.name === 'firstname' || el.name === 'lastname') validateNoDuplicate();
    }, true);

    // ✅ Efface l'erreur dès que l'utilisateur corrige
    form.addEventListener('input', function (e) {
        var el = e.target;
        if (el.name && RULES[el.name] && (el.value || '').trim() !== '') {
            clearField(el);
        }
        if (el.name === 'firstname' || el.name === 'lastname') validateNoDuplicate();
    });
    form.addEventListener('change', function (e) {
        var el = e.target;
        if (el.type === 'radio' && el.name && RULES[el.name]) {
            form.querySelectorAll('input[name="' + el.name + '"]').forEach(function (r) { clearField(r); });
            validateField(el);
        }
        if (el.name && RULES[el.name] && el.tagName === 'SELECT' && el.value) {
            clearField(el);
        }
    });

    // ✅ Soumission : vérifier tous les champs, bloquer si manquants
    form.addEventListener('submit', function (e) {
        var missing = [];
        var firstMissing = null;

        form.querySelectorAll('input[name], select[name]').forEach(function (el) {
            var name = el.name;
            if (!RULES[name]) return;
            var ok = validateField(el);
            if (!ok) {
                missing.push(RULES[name].msg);
                if (!firstMissing) firstMissing = el;
            }
        });

        // 🔞 Date de naissance (âge >= 3, < 18 si enfant)
        if (!validateBirthDate()) {
            missing.push('⚠ تاريخ الميلاد غير صحيح');
            if (!firstMissing) firstMissing = form.querySelector('input[name="birth_date"]');
        }

        // 🚫 Anti-doublon du même enfant
        if (!validateNoDuplicate()) {
            missing.push('⚠ هذا الطفل مسجل بالفعل');
            if (!firstMissing) firstMissing = form.querySelector('input[name="firstname"]');
        }

        if (missing.length > 0) {
            e.preventDefault();
            e.stopPropagation();
            var firstGroup = firstMissing ? groupOf(firstMissing) : null;
            if (firstGroup) firstGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (firstMissing) {
                if (firstMissing.type === 'radio') {
                    var r = form.querySelector('input[name="' + firstMissing.name + '"]');
                    if (r) r.focus();
                } else {
                    firstMissing.focus();
                }
            }
        }
    });
});
</script>
@endpush

@endsection
