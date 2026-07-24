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
        <div class="col-md-6 mb-3">
            <label class="form-label">المهنة</label>
            <input type="text" name="profession"
                   class="form-control @error('profession') is-invalid @enderror"
                   value="{{ old('profession', $person->profession ?? '') }}"
                   placeholder="مثال: طالب، مهندس، مستخدم...">
            @error('profession')
                <div class="form-error text-danger small">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <div class="mt-4 text-center">
        <button class="btn btn-success px-5">التالي</button>
    </div>

</form>
