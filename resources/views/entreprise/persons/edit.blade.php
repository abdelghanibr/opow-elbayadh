@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; text-align:right; max-width: 900px;">

    {{-- 🟦 Card --}}
    <div class="card shadow-lg border-0 rounded-4">

        {{-- Header --}}
        <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">✏️ تعديل بيانات المستخدم</h5>
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
            <form action="{{ route('entreprise.persons.update', $person->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="row g-4">

                    {{-- الاسم --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الاسم</label>
                        <input type="text" name="firstname"
                               class="form-control form-control-lg rounded-3"
                               value="{{ old('firstname', $person->firstname) }}"
                               required>
                    </div>

                    {{-- اللقب --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">اللقب</label>
                        <input type="text" name="lastname"
                               class="form-control form-control-lg rounded-3"
                               value="{{ old('lastname', $person->lastname) }}"
                               required>
                    </div>

                    {{-- تاريخ الميلاد --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">تاريخ الميلاد</label>
                        <input type="text" name="birth_date"
                               class="form-control js-date-fr form-control-lg rounded-3"
                               value="{{ old('birth_date', $person->birth_date) }}"
                               required>
                    </div>

                    {{-- الجنس --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الجنس</label>
                        <select name="gender"
                                class="form-select form-select-lg rounded-3" required>
                            <option value="">— اختر —</option>
                            <option value="ذكر" {{ old('gender', $person->gender)=='ذكر'?'selected':'' }}>ذكر</option>
                            <option value="أنثى" {{ old('gender', $person->gender)=='أنثى'?'selected':'' }}>أنثى</option>
                        </select>
                    </div>

                    {{-- التصنيف --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">التصنيف</label>
                        <select name="education"
                                class="form-select form-select-lg rounded-3" required>
                            <option value="">— اختر —</option>
                            @foreach(['لاعب','مدرب','مسير','آخر'] as $role)
                                <option value="{{ $role }}"
                                    {{ old('education', $person->education)==$role?'selected':'' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- الصورة --}}
                   <div class="col-md-6">
    <label class="form-label fw-bold">📷 الصورة الشمسية</label>

    <input type="file"
           name="photo"
           id="photoInput"
           class="form-control form-control-lg rounded-3"
           accept="image/jpeg,image/png">

    {{-- Preview --}}
    <div class="text-center mt-3">

        @if($person->photo)
            <img id="photoPreview"
                 src="{{ asset($person->photo) }}"
                 alt="Photo"
                 class="rounded-circle shadow-sm"
                 style="width:120px;height:120px;object-fit:cover;">
        @else
            <img id="photoPreview"
                 src="{{ asset('images/avatar-placeholder.png') }}"
                 alt="No photo"
                 class="rounded-circle shadow-sm"
                 style="width:120px;height:120px;object-fit:cover;">
        @endif

    </div>

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
                </div>

                {{-- زر الحفظ --}}
                <div class="text-center mt-5">
                    <button type="submit"
                            class="btn btn-success btn-lg px-5 rounded-pill shadow">
                        💾 حفظ التعديلات
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

.card {
    animation: fadeUp .5s ease-in-out;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

{{-- ================= JS (Preview) ================= --}}
<script>
document.getElementById('photoInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('photoPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);
});
</script>
@endsection
