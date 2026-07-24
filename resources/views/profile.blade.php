@extends('layouts.app')

@section('content')

<style>
.step-active {
    background: #16a34a !important;
    color: #fff !important;
}
.step-circle {
    width: 32px;
    height: 32px;
    background: #ccc;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
}
</style>

<div class="container">

    <h3 class="text-center my-3">استكمال البيانات</h3>

    <!-- شريط الخطوات -->
    <div class="d-flex justify-content-around mb-4">
        <div class="step-circle {{ $step == 1 ? 'step-active' : '' }}">1</div>
        <div class="step-circle {{ $step == 2 ? 'step-active' : '' }}">2</div>
        <div class="step-circle {{ $step == 3 ? 'step-active' : '' }}">3</div>
        <div class="step-circle {{ $step == 4 ? 'step-active' : '' }}">4</div>
    </div>

    <!-- نموذج عام -->
    <form method="POST" action="{{ route('profile.step.save', $step) }}" enctype="multipart/form-data">
        @csrf

        {{-- =======================  المرحلة الأولى  ======================= --}}
        @if ($step == 1)

        <h4 class="mb-4">المعلومات الأساسية</h4>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>الاسم</label>
                <input type="text" name="firstname" class="form-control" value="{{ $user->firstname }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>اللقب</label>
                <input type="text" name="lastname" class="form-control" value="{{ $user->lastname }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>تاريخ الميلاد</label>
                <input type="text" name="birth_date" class="form-control js-date-fr" value="{{ $user->birth_date }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>بلدية الميلاد</label>
                <input type="text" name="birth_city" class="form-control" value="{{ $user->birth_city }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>الجنس</label><br>
                <input type="radio" name="gender" value="ذكر" {{ $user->gender == 'ذكر' ? 'checked' : '' }}> ذكر
                <input type="radio" name="gender" value="أنثى" class="ms-3" {{ $user->gender == 'أنثى' ? 'checked' : '' }}> أنثى
            </div>

            <div class="col-md-6 mb-3">
                <label>هل لديك احتياجات خاصة؟</label><br>
                <input type="radio" name="handicap" value="1" {{ $user->handicap == 1 ? 'checked' : '' }}> نعم
                <input type="radio" name="handicap" value="0" class="ms-3" {{ $user->handicap == 0 ? 'checked' : '' }}> لا
            </div>
        </div>

        <button class="btn btn-success px-5">التالي</button>

        @endif

        {{-- =======================  المرحلة الثانية  ======================= --}}
        @if ($step == 2)

        <h4 class="mb-4">معلومات الولي</h4>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>اسم الولي</label>
                <input type="text" name="parent_name" class="form-control" value="{{ $user->parent_name }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>لقب الولي</label>
                <input type="text" name="parent_lastname" class="form-control" value="{{ $user->parent_lastname }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>تاريخ ميلاد الولي</label>
                <input type="text" name="parent_birth" class="form-control" value="{{ $user->parent_birth }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>بلدية ميلاد الولي</label>
                <input type="text" name="parent_birth_city" class="form-control" value="{{ $user->parent_birth_city }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>الجنس</label><br>
                <input type="radio" name="parent_gender" value="ذكر" {{ $user->parent_gender == 'ذكر' ? 'checked' : '' }}> ذكر
                <input type="radio" name="parent_gender" value="أنثى" class="ms-3" {{ $user->parent_gender == 'أنثى' ? 'checked' : '' }}> أنثى
            </div>

            <div class="col-md-6 mb-3">
                <label>رقم هاتف الولي</label>
                <input type="text" name="parent_phone" class="form-control" value="{{ $user->parent_phone }}">
            </div>
        </div>

        <button formaction="{{ route('profile.step.save', 1) }}" class="btn btn-secondary">السابق</button>
        <button class="btn btn-success px-5">التالي</button>

        @endif


        {{-- =======================  المرحلة الثالثة  ======================= --}}
        @if ($step == 3)

        <h4 class="mb-4">معلومات إضافية</h4>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>رقم الهاتف</label>
                <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>العنوان</label>
                <input type="text" name="address" class="form-control" value="{{ $user->address }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>الولاية</label>
                <input type="text" name="wilaya" class="form-control" value="{{ $user->wilaya }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>النشاط المفضل</label>
                <input type="text" name="favorite_activity" class="form-control" value="{{ $user->favorite_activity }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>المستوى الدراسي</label>
                <input type="text" name="level" class="form-control" value="{{ $user->level }}">
            </div>
        </div>

        <button formaction="{{ route('profile.step.save', 2) }}" class="btn btn-secondary">السابق</button>
        <button class="btn btn-success px-5">التالي</button>

        @endif


        {{-- =======================  المرحلة الرابعة  ======================= --}}
        @if ($step == 4)

        <h4 class="mb-4">الوثائق المطلوبة</h4>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>صورة شخصية</label>
                <input type="file" name="document_photo" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label>شهادة الميلاد (PDF)</label>
                <input type="file" name="document_birth" class="form-control">
            </div>
        </div>

        <button formaction="{{ route('profile.step.save', 3) }}" class="btn btn-secondary">السابق</button>
        <button class="btn btn-success px-5">حفظ</button>

        @endif

    </form>

</div>

@endsection
