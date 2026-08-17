@extends('layouts.app')

@section('title', 'تسجيل فرد جديد')

@section('content')

<div class="register-bg-person">

    <div class="register-box-person">

        <!-- Header -->
        <div class="text-center mb-3">
            <img src="{{ asset('images/djs-logo.png') }}" width="90">
          <h3 class="fw-bold mt-2">تسجيل كممارس جديد</h3>
        </div>

        <form method="POST" action="{{ route('person.register.post') }}" id="personRegisterForm" novalidate>
            @csrf

            <div class="row g-4">
        @if($selectedComplex)
    <div class="alert alert-info text-center fw-bold">
        التسجيل في المركب: <span class="text-primary">{{ $selectedComplex->nom }}</span>
        <br>
        <small>رقم المركب: {{ $selectedComplex->id }}</small>
    </div>
@endif

{{-- اختيار النشاط --}}
@if(isset($activities) && count($activities) > 0)
    <label class="form-label fw-bold">اختر النشاط</label>
    <select name="activity_id" class="form-control mb-3">
        @foreach($activities as $activity)
            <option value="{{ $activity->id }}">{{ $activity->title }}</option>
        @endforeach
    </select>
@else
    <p class="text-muted">⚠ الرجاء اختيار مجمع أولاً لعرض الأنشطة المتاحة.</p>
@endif


                <!-- Full Name -->
                <div class="col-md-6">
                    <label class="form-label">الاسم الكامل</label>
                    <input type="text" name="name" id="reg_name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" required>

                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div id="reg_name_feedback" class="mt-1" style="font-size:0.9rem;"></div>
                </div>

                <!-- Email -->
              <div class="col-md-6">
    <label class="form-label">البريد الإلكتروني</label>
    <input type="email" name="email" id="reg_email" value="{{ old('email') }}"
        class="form-control @error('email') is-invalid @enderror"
        placeholder="nomprenom@gmail.com" required>

    <div class="text-muted mt-1" style="font-size: 0.9rem;">
        يرجى إدخال بريد إلكتروني، مثال: nomprenom@gmail.com
    </div>

    @error('email')
        <div class="form-error">{{ $message }}</div>
    @enderror
    <div id="reg_email_feedback" class="mt-1" style="font-size:0.9rem;"></div>
</div>
<div class="mt-3">
  <label for="nin" class="fw-bold">
    🔢 رقم التعريف الوطني (NIN) / رقم تعريف الولي للقاصر
</label>

    <input type="text"
           name="nin"
           id="reg_nin"
           class="form-control @error('nin') is-invalid @enderror"
           placeholder="أدخل رقم التعريف الوطني المكوّن من 18 رقمًا أو رقم التعريف الوطني للولي في حالة القصر"
           maxlength="18"
           inputmode="numeric"
           required>

    @error('nin')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    <div id="reg_nin_feedback" class="mt-1" style="font-size:0.9rem;"></div>
</div>

                <!-- Complex -->

<input type="hidden" name="complex_id" value="{{ $complexId }}">


                <!-- Password -->
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" id="reg_password"
                        class="form-control @error('password') is-invalid @enderror" required>

                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div id="reg_password_feedback" class="mt-1" style="font-size:0.9rem;"></div>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" id="reg_password_confirmation" class="form-control" required>
                    <div id="reg_password_confirmation_feedback" class="mt-1" style="font-size:0.9rem;"></div>
                </div>

                <!-- Robot Check -->
<div class="mt-3">
    <label class="fw-bold d-block mb-1">🔐 التحقق أنني لست روبوت :</label>

    <p class="text-muted mb-2">
        يرجى كتابة الكلمة التالية كما هي تماماً:
    </p>

    <!-- الكلمة داخل إطار جميل -->
    <div style="
        border: 2px dashed #007bff;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 1.3rem;
        font-weight: bold;
        text-align: center;
        background: #f8f9fa;
        color: #007bff;
        letter-spacing: 2px;
        margin-bottom: 15px;
        
    ">
        {{ $correctWord }}
    </div>

    <input type="text" 
           name="captcha_word" 
           id="reg_captcha"
           class="form-control text-center"
           placeholder="اكتب الكلمة هنا" 
           required>

    @error('captcha_word')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    <div id="reg_captcha_feedback" class="mt-1" style="font-size:0.9rem;"></div>
</div>




                <!-- Privacy Zone -->
               <div class="legal-zone-person mt-4">

    <div class="legal-card">
        <h5 class="legal-title">الشروط والأحكام العامة وحماية المعطيات الشخصية</h5>

        <div class="legal-text">
      <p>
    أوافق على
    <a href="{{ route('legal.terms') }}" target="_blank" class="legal-link">
        الشروط والأحكام العامة
    </a>
    لاستعمال المنصة والخدمات المقدمة من طرف ديوان المركب المتعدد الرياضات لولاية البيض،
    وألتزم باحترام القانون الداخلي للمركب.
</p>

<p id="registerPledge">
                أتعهد بأن جميع المعلومات المصرح بها في هذه الاستمارة صحيحة ودقيقة،
                وأتحمل كامل المسؤولية في حال ثبوت خلاف ذلك.
            </p>

            <p>
                أوافق على جمع ومعالجة معطياتي الشخصية واستعمالها حصريًا في إطار تسيير
                الانخراطات والخدمات الرياضية وفقًا لأحكام
                <a href="{{ route('legal.privacy') }}" target="_blank" class="legal-link">
                    سياسة الخصوصية (القانون 18-07)
                </a>
                المتعلق بحماية الأشخاص الطبيعيين في معالجة المعطيات ذات الطابع الشخصي.
            </p>
        </div>

        <div class="form-check legal-check mt-3">
            <input
                class="form-check-input @error('privacy_policy') is-invalid @enderror"
                type="checkbox"
                name="privacy_policy"
                id="privacy_policy"
                value="1"
                required
            >

            <label class="form-check-label" for="privacy_policy">
                أؤكد أنني قرأت وفهمت الشروط والأحكام العامة وسياسة الخصوصية،
                وأوافق على ما ورد فيهما، وأتحمل مسؤولية صحة جميع المعلومات التي أدخلتها.
            </label>

            @error('privacy_policy')
                <div class="invalid-feedback d-block mt-1">
                    يجب الموافقة على الشروط والأحكام وسياسة الخصوصية
                </div>
            @enderror
        </div>
    </div>

</div>

                <!-- Submit -->
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-success w-100 py-2">
                        إنشاء حساب
                    </button>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                        {{ $errors->first() }}
                    </div>
                @endif

            </div>

        </form>

        <p class="text-center mt-3">
            لديك حساب بالفعل؟ <a href="{{ route('person.login') }}">تسجيل الدخول</a>
        </p>

    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- CSS --}}
<style>

.register-bg-person {
    background:#e8f5e9;
    padding:30px;
}

.register-box-person {
    background:white;
    width:95%;
    max-width:900px;
    margin:auto;
    padding:40px;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.12);
}

.form-error {
    color:#b71c1c;
    font-size:.9rem;
    margin-top:4px;
}

.privacy-zone-person,
.legal-zone-person {
    width: 100%;
    margin-top: 1rem;
    direction: rtl;
}

.privacy-zone-person .legal-card,
.legal-zone-person .legal-card {
    background: #e6f7ff;               /* bleu ciel */
    border: 1px solid #b9e6ff;
    border-radius: 18px;               /* bakroune */
    padding: 14px 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    width: 100%;
    overflow-wrap: break-word;
}

.legal-title {
    text-align: center;
    font-weight: 800;
    font-size: clamp(12px, 2.8vw, 16px);
    color: #222;
    margin-bottom: 10px;
    line-height: 1.6;
}

.legal-text p {
    color: #444;
    font-size: clamp(11px, 2.7vw, 14px);
    line-height: 1.9;
    margin-bottom: 10px;
    text-align: justify;
}

.legal-link {
    color: #0d6efd;
    font-weight: 700;
    text-decoration: underline;
    font-size: inherit;
    word-break: break-word;
}

.legal-check {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-top: 10px;
}

.legal-check .form-check-input {
    flex: 0 0 auto;
    width: 16px;
    height: 16px;
    margin: 3px 0 0 0;
    cursor: pointer;
}

.legal-check .form-check-label {
    flex: 1;
    color: #333;
    font-size: clamp(11px, 2.7vw, 14px);
    line-height: 1.8;
    margin: 0;
    word-break: break-word;
}

/* Très petits écrans */
@media (max-width: 576px) {
    .privacy-zone-person .legal-card,
    .legal-zone-person .legal-card {
        padding: 12px 10px;
        border-radius: 14px;
    }

    .legal-title {
        font-size: 13px;
        margin-bottom: 8px;
    }

    .legal-text p,
    .legal-check .form-check-label {
        font-size: 12px;
        line-height: 1.8;
    }

    .legal-check .form-check-input {
        width: 15px;
        height: 15px;
    }
}
</style>

<script>
(function () {
    const form = document.getElementById('personRegisterForm');
    const captchaWord = {!! json_encode(strtolower($correctWord)) !!};
    const checkEmailUrl = "{{ route('person.check-email') }}";

    function el(id) { return document.getElementById(id); }

    const rules = {
        reg_name: { check: v => v.trim().length >= 2, ok: 'الاسم صحيح ✔', err: 'الاسم الكامل مطلوب (حرفان على الأقل)' },
        reg_email: { ok: 'البريد متوفر ✔', err: 'صيغة البريد الإلكتروني غير صحيحة', taken: 'هذا البريد الإلكتروني مستعمل بالفعل' },
        reg_nin: { check: v => /^\d{18}$/.test(v), ok: 'NIN صحيح (18 رقمًا) ✔', err: 'يجب أن يتكون من 18 رقمًا بالضبط' },
        reg_password: { check: v => v.length >= 8, ok: 'قوة كلمة المرور مقبولة ✔', err: 'كلمة المرور يجب أن تكون 8 أحرف على الأقل' },
        reg_password_confirmation: { check: v => v === el('reg_password').value, ok: 'كلمتا المرور متطابقتان ✔', err: 'كلمتا المرور غير متطابقتين' },
        reg_captcha: { check: v => v.trim().toLowerCase() === captchaWord, ok: 'التحقق صحيح ✔', err: 'الكلمة غير صحيحة، أعد كتابتها' },
    };

    function setState(input, feedback, ok, msg) {
        input.classList.remove('is-valid', 'is-invalid');
        feedback.classList.remove('text-success', 'text-danger', 'text-muted');
        if (ok) {
            input.classList.add('is-valid');
            feedback.classList.add('text-success');
            feedback.innerHTML = '✔ ' + msg;
        } else {
            input.classList.add('is-invalid');
            feedback.classList.add('text-danger');
            feedback.innerHTML = '✘ ' + msg;
        }
    }

    function pending(input, feedback, msg) {
        input.classList.remove('is-valid', 'is-invalid');
        feedback.classList.remove('text-success', 'text-danger');
        feedback.classList.add('text-muted');
        feedback.innerHTML = '⏳ ' + msg;
    }

    // NIN : chiffres uniquement, max 18
    el('reg_nin').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 18);
    });

    // Email : vérification de disponibilité (AJAX)
    function checkEmailAvailability(email) {
        return fetch(checkEmailUrl + '?email=' + encodeURIComponent(email), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(d => d.available);
    }

    let emailTimer = null;
    function liveEmailCheck() {
        const input = el('reg_email');
        const fb = el('reg_email_feedback');
        const email = input.value.trim();
        const fmtOk = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email);
        if (!fmtOk) {
            setState(input, fb, false, rules.reg_email.err);
            return;
        }
        pending(input, fb, 'جاري التحقق من التوفر...');
        clearTimeout(emailTimer);
        emailTimer = setTimeout(() => {
            checkEmailAvailability(email).then(available => {
                setState(input, fb, available, available ? rules.reg_email.ok : rules.reg_email.taken);
            }).catch(() => setState(input, fb, true, rules.reg_email.ok));
        }, 500);
    }

    function validateField(name) {
        const input = el(name);
        if (!input) return true;
        const fb = el(name + '_feedback');
        const rule = rules[name];
        if (!rule) return true;
        if (name === 'reg_email') {
            const email = input.value.trim();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
                setState(input, fb, false, rule.err);
                return false;
            }
            return true; // la disponibilité est vérifiée en AJAX (submit)
        }
        const ok = rule.check(input.value);
        setState(input, fb, ok, ok ? rule.ok : rule.err);
        return ok;
    }

    Object.keys(rules).forEach(function (name) {
        const input = el(name);
        if (input) input.addEventListener('input', function () { validateField(name); });
    });

    el('reg_email').addEventListener('input', liveEmailCheck);

    el('reg_password').addEventListener('input', function () {
        if (el('reg_password_confirmation').value !== '') validateField('reg_password_confirmation');
    });

    // Blocage de l'envoi tant que tout n'est pas valide
    form.addEventListener('submit', async function (e) {
        let allOk = true;

        for (const name of Object.keys(rules)) {
            if (name === 'reg_email') {
                const input = el('reg_email');
                const fb = el('reg_email_feedback');
                const email = input.value.trim();
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
                    setState(input, fb, false, rules.reg_email.err);
                    allOk = false;
                    continue;
                }
                pending(input, fb, 'جاري التحقق من التوفر...');
                try {
                    const available = await checkEmailAvailability(email);
                    if (!available) { setState(input, fb, false, rules.reg_email.taken); allOk = false; }
                    else setState(input, fb, true, rules.reg_email.ok);
                } catch (err) { setState(input, fb, true, rules.reg_email.ok); }
                continue;
            }
            if (!validateField(name)) allOk = false;
        }

        const privacy = el('privacy_policy');
        if (privacy && !privacy.checked) {
            allOk = false;
            privacy.classList.add('is-invalid');
        } else if (privacy) {
            privacy.classList.remove('is-invalid');
        }

        if (!allOk) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
})();
</script>

@endsection
