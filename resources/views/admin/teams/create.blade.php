@extends('layouts.app')
@include('admin.partials.theme-admin')

@section('content')
<div class="container py-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10 col-12">

            <div class="card-modern rounded-4 shadow-lg">
                <div class="card-header text-center py-4">
                    <h3 class="fw-bold">➕ إضافة فريق جديد</h3>
                    <p class="small">قم بإدخال اسم الفريق وتحميل الشعار</p>
                </div>

                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4 shadow-sm mb-4">
                            <ul class="mb-0 fw-semibold">
                                @foreach ($errors->all() as $error)
                                    <li>⚠ {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('teams.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Team Name -->
                        <div class="mb-3">
                            <label class="form-label">اسم الفريق *</label>
                            <input type="text" name="name" class="form-control form-control-modern" placeholder="مثال: مولودية الجزائر" required>
                        </div>

                        <!-- Logo Upload with Preview -->
                        <div class="mb-4 text-center">
                            <label class="form-label">شعار الفريق</label>
                            <div class="image-circle mx-auto mb-3" onclick="document.getElementById('logo').click()">
                                <span class="image-placeholder">اضغط لاختيار صورة</span>
                            </div>

                            <input type="file" name="logo" id="logo" class="form-control modern-file" accept="image/*" onchange="previewCircleImage(this)">
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary rounded-pill px-5">رجوع</a>
                            <button class="btn btn-primary btn-glow rounded-pill px-5">💾 حفظ الفريق</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
function previewCircleImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            let circle = input.previousElementSibling;
            circle.innerHTML = `<img src="${e.target.result}">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
