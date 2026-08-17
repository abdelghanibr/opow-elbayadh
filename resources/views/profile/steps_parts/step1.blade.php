<form action="{{ route('profile.step.save', 1) }}" method="POST">
    @csrf

    <h4 class="mb-4 fw-bold">المعلومات الأساسية</h4>

    <div class="row">

        <!-- الاسم -->
        <div class="col-md-6 mb-3">
            <label class="form-label">الاسم</label>
            <input type="text" name="firstname"
                   class="form-control @error('firstname') is-invalid @enderror"
                   value="{{ old('firstname', $person->firstname ?? '') }}">
            @error('firstname')
                <div class="form-error text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <!-- اللقب -->
        <div class="col-md-6 mb-3">
            <label class="form-label">اللقب</label>
            <input type="text" name="lastname"
                   class="form-control @error('lastname') is-invalid @enderror"
                   value="{{ old('lastname', $person->lastname ?? '') }}">
            @error('lastname')
                <div class="form-error text-danger small">{{ $message }}</div>
            @enderror
        </div>
<div class="col-md-6 mb-3">
    <label class="form-label">اسم الأب</label>
    <input type="text"
           name="tuteur_fullname"
           class="form-control @error('tuteur_fullname') is-invalid @enderror"
           value="{{ old('tuteur_fullname', $person->tuteur_fullname ?? '') }}">

    @error('tuteur_fullname')
        <div class="form-error text-danger small">{{ $message }}</div>
    @enderror
</div>

        <!-- تاريخ الميلاد -->
        <div class="col-md-6 mb-3">
            <label class="form-label">تاريخ الميلاد</label>
            <input type="text" name="birth_date"
                   class="form-control js-date-fr @error('birth_date') is-invalid @enderror"
                   value="{{ old('birth_date', $person->birth_date ?? '') }}">
            @error('birth_date')
                <div class="form-error text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <!-- الجنس -->
      <div class="col-md-6 mb-3">
    <label class="form-label d-block">الجنس</label>

    <label class="ms-3">
        <input type="radio" name="gender" value="H"
               {{ old('gender', $person->gender ?? '') == 'H' ? 'checked' : '' }}>
        ذكر
    </label>

    <label class="ms-3">
        <input type="radio" name="gender" value="F"
               {{ old('gender', $person->gender ?? '') == 'F' ? 'checked' : '' }}>
        أنثى
    </label>

    @error('gender')
        <div class="form-error text-danger small">{{ $message }}</div>
    @enderror
</div>


        <!-- احتياجات خاصة -->
        <div class="col-md-6 mb-3">
            <label class="form-label d-block">هل لديك احتياجات خاصة؟</label>

            <label class="ms-3">
                <input type="radio" name="handicap" value="1"
                {{ old('handicap', $person->handicap ?? '') == 1 ? 'checked' : '' }}>
                نعم
            </label>

            <label class="ms-3">
                <input type="radio" name="handicap" value="0"
                {{ old('handicap', $person->handicap ?? '') == 0 ? 'checked' : '' }}>
                لا
            </label>

            @error('handicap')
                <div class="form-error text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <!-- فصيلة الدم -->
        <div class="col-md-6 mb-3">
            <label class="form-label">فصيلة الدم</label>
            <select name="blood_type" class="form-control @error('blood_type') is-invalid @enderror">
                <option value="">— اختر فصيلة الدم —</option>
                @foreach(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'] as $bt)
                    <option value="{{ $bt }}" {{ old('blood_type', $person->blood_type ?? '') == $bt ? 'selected' : '' }}>
                        {{ $bt }}
                    </option>
                @endforeach
            </select>
            @error('blood_type')
                <div class="form-error text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <!-- المهنة -->
        @if(!($isChild ?? false))
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold" style="color:#082f57; font-size:0.92rem;">
                <i class="fa-solid fa-briefcase" style="color:#12a86b; margin-left:6px;"></i>المهنة
            </label>
            <div style="position:relative;">
                <input type="text" name="profession"
                       class="form-control @error('profession') is-invalid @enderror"
                       value="{{ old('profession', $person->profession ?? '') }}"
                       placeholder="مثال: طالب، مهندس، طبيب، مستخدم..."
                       style="border:2px solid #e2e8f0; border-radius:12px; padding:12px 16px 12px 44px; font-weight:700; font-size:0.95rem; background:#f8fafc; transition:all 0.25s ease;"
                       onfocus="this.style.borderColor='#12a86b'; this.style.boxShadow='0 0 0 4px rgba(18,168,107,0.12)'; this.style.background='#fff'"
                       onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc'">
                <i class="fa-solid fa-briefcase" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:1rem; pointer-events:none;"></i>
            </div>
            @error('profession')
                <div class="form-error text-danger small">{{ $message }}</div>
            @enderror
        </div>
        @endif

    </div>

    {{-- ملاحظة إعادة التحقق --}}
    <div class="alert alert-warning d-flex align-items-center gap-2 mt-3" style="border-radius:12px; font-size:0.9rem;">
        <i class="fa-solid fa-circle-exclamation" style="font-size:1.2rem;"></i>
        <div>
            <strong>تنبيه:</strong> بعد تعديل أي معلومات شخصية، سيتم إلغاء تأكيد حسابك الحالي وستنتظر <strong>_validation من الإدارة</strong> مرة أخرى قبل استعمال المنصة.
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-between">
        <a href="{{ route('person.dashboard') }}" class="btn btn-outline-secondary px-4">
            <i class="fa-solid fa-arrow-right"></i> إلغاء — العودة للوحة التحكم
        </a>
        <button class="btn btn-success px-5">
            <i class="fa-solid fa-arrow-left"></i> التالي
        </button>
    </div>

</form>
