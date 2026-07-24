@extends('layouts.app')
@include('admin.partials.theme-admin')

@section('content')
<div class="container py-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">

            <div class="card-modern rounded-4 shadow-lg">
                
                <div class="card-header text-center py-4">
                    <h3 class="fw-bold">➕ إضافة توزيع مقاعد جديد</h3>
                    <p class="small text-muted">حدد المركب ونوع المقعد والعدد الإجمالي للمقاعد</p>
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

                    <form action="{{ route('complex_seats.store') }}" method="POST">
                        @csrf

                        <!-- Select Complex -->
                        <div class="mb-3">
                            <label class="form-label">المركب *</label>
                            <select name="complex_id" class="form-select form-control-modern" required>
                                <option disabled selected>اختر المركب</option>
                                @foreach($complexes as $c)
                                    <option value="{{ $c->id }}">{{ $c->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Select Seat Type -->
                        <div class="mb-3">
                            <label class="form-label">نوع المقعد *</label>
                            <select name="seat_type_id" class="form-select form-control-modern" required>
                                <option disabled selected>اختر نوع المقعد</option>
                                @foreach($seatTypes as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ number_format($t->price) }} دج)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Total Seats -->
                        <div class="mb-3">
                            <label class="form-label">عدد المقاعد *</label>
                            <input type="number"
                                   name="total_seats"
                                   class="form-control form-control-modern"
                                   placeholder="أدخل عدد المقاعد">
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('complex_seats.index') }}" class="btn btn-outline-secondary rounded-pill px-5">
                                رجوع
                            </a>
                            <button class="btn btn-primary btn-glow rounded-pill px-5">
                                💾 حفظ التوزيع
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
