@extends('layouts.app')
@include('admin.partials.theme-admin')

@section('content')
<div class="container py-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10 col-12">

            <div class="card-modern rounded-4 shadow-lg">
                <div class="card-header text-center">
                    <h3 class="fw-bold">✏ تعديل نوع المقعد</h3>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('seat_types.update', $seat_type->id) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">اسم النوع *</label>
                            <input type="text"
                                   name="name"
                                   value="{{ $seat_type->name }}"
                                   class="form-control-modern form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">السعر *</label>
                            <input type="number"
                                   step="0.01"
                                   name="price"
                                   value="{{ $seat_type->price }}"
                                   class="form-control-modern form-control">
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('seat_types.index') }}" class="btn btn-outline-secondary rounded-pill px-5">رجوع</a>
                            <button class="btn btn-primary btn-glow rounded-pill px-5">💾 حفظ التعديلات</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
