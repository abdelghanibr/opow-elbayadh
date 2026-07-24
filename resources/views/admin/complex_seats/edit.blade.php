@extends('layouts.app')
@include('admin.partials.theme-admin')

@section('content')
<div class="container py-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">

            <div class="card-modern shadow-lg rounded-4">

                <div class="card-header text-center py-4">
                    <h3 class="fw-bold">✏ تعديل توزيع المقاعد</h3>
                    <p class="small">يمكنك تعديل عدد المقاعد المتاحة لهذا المركب وهذا النوع</p>
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

                    <form action="{{ route('complex_seats.update', $complexSeat->id) }}" method="POST">
                        @csrf @method('PUT')

                        <!-- Complex (read only) -->
                        <div class="mb-3">
                            <label class="form-label">المركب</label>
                            <input type="text"
                                   value="{{ $complexSeat->complex->nom }}"
                                   class="form-control form-control-modern"
                                   disabled>
                        </div>

                        <!-- Seat type (read only) -->
                        <div class="mb-3">
                            <label class="form-label">نوع المقعد</label>
                            <input type="text"
                                   value="{{ $complexSeat->seatType->name }}"
                                   class="form-control form-control-modern"
                                   disabled>
                        </div>

                        <!-- Total Seats -->
                        <div class="mb-3">
                            <label class="form-label">عدد المقاعد *</label>
                            <input type="number"
                                   name="total_seats"
                                   value="{{ $complexSeat->total_seats }}"
                                   class="form-control form-control-modern">
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('complex_seats.index') }}" class="btn btn-outline-secondary rounded-pill px-5">رجوع</a>
                            <button class="btn btn-primary btn-glow rounded-pill px-5">💾 حفظ التعديلات</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
