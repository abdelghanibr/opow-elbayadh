@extends('layouts.app')
@include('admin.partials.theme-admin')

@section('content')
<div class="container py-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10 col-12">

            <div class="card-modern shadow-lg rounded-4">
                <div class="card-header text-center py-4">
                    <h3 class="fw-bold">➕ إضافة نوع مقعد جديد</h3>
                    <p class="small">حدد اسم النوع والسعر الأساسي للتذكرة</p>
                </div>

                <div class="card-body p-4">

                    <form method="POST" action="{{ route('seat_types.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">اسم النوع *</label>
                            <input name="name"
                                   type="text"
                                   class="form-control form-control-modern"
                                   placeholder="مثال: VIP / Standard">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">السعر *</label>
                            <input name="price"
                                   type="number"
                                   step="0.01"
                                   class="form-control form-control-modern"
                                   placeholder="مثال: 1500">
                        </div>

                        <div class="d-flex justify-content-end mt-4 gap-3">
                            <a href="{{ route('seat_types.index') }}" class="btn btn-outline-secondary px-5 rounded-pill">رجوع</a>
                            <button class="btn btn-primary btn-glow px-5 rounded-pill">💾 حفظ</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

