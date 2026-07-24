@extends('layouts.app')
@include('admin.partials.theme-admin')

@section('content')
<div class="container py-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card-modern shadow-lg rounded-4">
                <div class="card-header text-center py-4">
                    <h3 class="fw-bold">✏ تعديل بيانات الفريق</h3>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('teams.update', $team->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label">اسم الفريق *</label>
                            <input type="text" name="name" value="{{ $team->name }}" class="form-control form-control-modern">
                        </div>

                        <!-- Logo Preview -->
                        <div class="mb-4 text-center">
                            <label class="form-label">الشعار الحالي</label>

                            <div class="image-circle mx-auto mb-3">
                                <img src="{{ asset($team->logo) }}">
                            </div>

                            <input type="file" name="logo" class="form-control modern-file mt-3" onchange="previewCircleImage(this)">
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary rounded-pill px-5">رجوع</a>
                            <button class="btn btn-primary btn-glow rounded-pill px-5">💾 حفظ التعديلات</button>
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
