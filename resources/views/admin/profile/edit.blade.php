@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">

    <h3 class="mb-4 fw-bold"><i class="fa-solid fa-user-gear ms-2"></i> تعديل الحساب (المدير)</h3>

    @if (session('success'))
    <div class="alert alert-success text-center fw-bold">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- الاسم --}}
        <label class="form-label fw-bold">👤 الاسم الكامل</label>
        <input type="text" name="name" class="form-control mb-3"
               value="{{ old('name', $user->name) }}" required>

        {{-- البريد --}}
        <label class="form-label fw-bold">📧 البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control mb-3"
               value="{{ old('email', $user->email) }}" required>

        {{-- رقم الهاتف --}}
        <label class="form-label fw-bold">📞 رقم الهاتف</label>
        <input type="text" name="phone" class="form-control mb-3"
               value="{{ old('phone', $user->phone) }}">

        {{-- كلمة المرور --}}
        <label class="form-label fw-bold">🔐 كلمة المرور الجديدة (اختياري)</label>
        <input type="password" name="password" class="form-control mb-3" placeholder="اتركه فارغاً إذا كنت لا تريد التغيير">

        <label class="form-label fw-bold">🔐 تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" class="form-control mb-4">

        <button type="submit" class="btn btn-primary fw-bold px-4">
            💾 حفظ التعديلات
        </button>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-4">
            رجوع
        </a>
    </form>

</div>
@endsection
