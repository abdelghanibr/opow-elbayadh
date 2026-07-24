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

        <form method="POST" action="{{ route('person.register.post') }}">
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
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" required>

                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
              <div class="col-md-6">
    <label class="form-label">البريد الإلكتروني</label>
    <input type="email" name="email" value="{{ old('email') }}"
        class="form-control @error('email') is-invalid @enderror"
        placeholder="nomprenom@gmail.com" required>

    <div class="text-muted mt-1" style="font-size: 0.9rem;">
        يرجى إدخال بريد إلكتروني، مثال: nomprenom@gmail.com
    </div>

    @error('email')
        <div class="form-error">{{ $message }}</div>
    @enderror
</div>
<div class="mt-3">
  <label for="nin" class="fw-bold">
    🔢 رقم التعريف الوطني (NIN) / رقم تعريف الولي للقاصر
</label>

    <input type="text"
           name="nin"
           id="nin"
           class="form-control @error('nin') is-invalid @enderror"
           placeholder="أدخل رقم التعريف الوطني المكوّن من 18 رقمًا أو رقم التعريف الوطني للولي في حالة القصر"
           maxlength="20"
           required>

    @error('nin')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>
<script>
document.getElementById('nin').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20);
});
</script>

                <!-- تاريخ الميلاد -->
                <div class="col-md-6">
                    <label class="form-label">تاريخ الميلاد</label>
                    <input type="date" name="birth_date" id="birth_date"
                           class="form-control @error('birth_date') is-invalid @enderror"
                           value="{{ old('birth_date') }}" required
                           onchange="toggleRegisterPledge()">
                    @error('birth_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- فصيلة الدم -->
                <div class="col-md-6">
                    <label class="form-label">فصيلة الدم</label>
                    <select name="blood_type" class="form-control @error('blood_type') is-invalid @enderror">
                        <option value="">— اختر —</option>
                        @foreach(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'] as $bt)
                            <option value="{{ $bt }}" {{ old('blood_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                        @endforeach
                    </select>
                    @error('blood_type')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- المهنة -->
                <div class="col-md-6">
                    <label class="form-label">المهنة</label>
                    <input type="text" name="profession"
                           class="form-control @error('profession') is-invalid @enderror"
                           value="{{ old('profession') }}"
                           placeholder="طالب، مهندس، مستخدم...">
                    @error('profession')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Complex -->

<input type="hidden" name="complex_id" value="{{ $complexId }}">


                <!-- Password -->
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" required>

                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
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
           class="form-control text-center"
           placeholder="اكتب الكلمة هنا" 
           required>

    @error('captcha_word')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
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

<p id="registerPledge" style="display:none;">
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
function toggleRegisterPledge() {
    var birthInput = document.getElementById('birth_date');
    if (!birthInput || !birthInput.value) return;
    var today = new Date();
    var b = new Date(birthInput.value);
    var age = today.getFullYear() - b.getFullYear();
    if (today.getMonth() < b.getMonth() || (today.getMonth() === b.getMonth() && today.getDate() < b.getDate())) {
        age--;
    }
    var pledgeEl = document.getElementById('registerPledge');
    if (pledgeEl) {
        pledgeEl.style.display = age >= 18 ? 'block' : 'none';
    }
}
document.addEventListener('DOMContentLoaded', toggleRegisterPledge);
</script>

@endsection
