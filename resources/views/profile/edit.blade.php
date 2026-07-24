@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">

    <h3 class="mb-4 fw-bold">👤 تعديل الملف الشخصي</h3>

    @if(session('success'))
        <div class="alert alert-success fw-bold text-center">{{ session('success') }}</div>
    @endif

    <form action="{{ route('person.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
{{-- المجمّع الرياضي (غير قابل للتعديل) --}}


{{-- المجمّع الرياضي --}}
<label class="form-label fw-bold">المجمّع الرياضي</label>

<select name="complex_id" class="form-select mb-3" required>
    @foreach($complexes as $complex)
        <option value="{{ $complex->id }}"
            {{ $user->complex_id == $complex->id ? 'selected' : '' }}>
            {{ $complex->nom }}
        </option>
    @endforeach
</select>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>⚠️ توجد أخطاء في الإدخال:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


        {{-- الاسم --}}
        <label class="form-label fw-bold">الاسم الكامل</label>
        <input type="text" name="name" class="form-control mb-3"
               value="{{ $user->name }}" required>

        {{-- البريد الإلكتروني --}}
        <label class="form-label fw-bold">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control mb-3"
               value="{{ $user->email }}" required>

   

        {{-- رقم التعريف الوطني NIN --}}
        <label class="form-label fw-bold">رقم التعريف الوطني (NIN)</label>
        <input type="text" name="nin" class="form-control mb-3"
               maxlength="18"
               value="{{ $user->nin }}" required>

        {{-- كلمة المرور الجديدة --}}
        <label class="form-label fw-bold">كلمة المرور الجديدة</label>
        <input type="password" name="password" class="form-control mb-3"
               placeholder="اتركه فارغاً إذا لم ترغب في تغيير كلمة المرور">

        {{-- تأكيد كلمة المرور --}}
        <label class="form-label fw-bold">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" class="form-control mb-3"
               placeholder="أعد إدخال كلمة المرور">

       

        <button class="btn btn-success fw-bold px-4">💾 حفظ</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary px-4">رجوع</a>

    </form>

</div>
@endsection
