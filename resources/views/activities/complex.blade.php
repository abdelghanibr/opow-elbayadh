@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right">

    <!-- Header Bar -->
    <div class="p-3 mb-4"
         style="background: linear-gradient(to right, #0a4f88, #0a8a67);
                border-radius: 10px; color:#fff; font-weight:600;">
        <div class="d-flex justify-content-between align-items-center">
            <span>📌 إدارة مركبات النشاط المختار</span>
            <span style="font-size:20px;">
                <i class="fa-solid fa-building-columns ms-2"></i>
                مركباتي
            </span>
        </div>
    </div>

    <!-- Title -->
    <h4 class="fw-bold mb-4 text-success text-center">
        🏟️ المركبات المتاحة لنشاط :
        <span style="color: {{ $activity->color }};">
            {{ $activity->title }}
        </span>
    </h4>

    <!-- Filters + Search -->
    <div class="d-flex justify-content-between mb-4 flex-wrap gap-3">

        <!-- Filters -->
        <div class="d-flex gap-4">
            <a href="#" class="text-success fw-bold text-decoration-none">
                جميع المركبات
            </a>
            <a href="#" class="text-warning fw-bold text-decoration-none">
                قيد الحجز
            </a>
            <a href="#" class="text-info fw-bold text-decoration-none">
                المتاحة حالياً
            </a>
        </div>

        <!-- Search -->
        <form method="GET" class="d-flex">
            <input type="text" name="search"
                   class="form-control js-date-fr"
                   placeholder="🔍 ابحث عن مركب..."
                   style="border-radius: 8px; width:200px;">
        </form>

    </div>

    <!-- Complexes Cards -->
    <div class="row g-4 justify-content-center">

        @forelse($activity->complexes as $complex)
        <div class="col-md-4 col-sm-6">

            <div class="card shadow-sm border-0"
                 style="border-top: 4px solid {{ $activity->color }}; border-radius:12px;
                        transition:0.3s;">
                
                <div class="card-body">

                    <h5 class="fw-bold text-success text-center mb-3">
                        {{ $complex->nom }}
                    </h5>

                    <p class="text-muted small text-center">
                        👥 السعة: {{ $complex->capacite }} شخص
                    </p>

                    <p class="fw-bold text-center" style="color:#0a4f88;">
                        💵 السعر:
                        <span class="text-success">
                            {{ number_format($complex->prix,2) }} دج
                        </span>
                    </p>

                    <div class="text-center">
                        <a href="{{ route('reservation.form', $complex->id) }}"
                           class="btn btn-success btn-sm px-4">
                            <i class="fa-solid fa-ticket ms-1"></i>
                            متابعة التسجيل
                        </a>
                    </div>

                </div>

            </div>

        </div>
        @empty

        <div class="alert alert-warning text-center w-100">
            🚫 لا يوجد مركبات مرتبطة بهذا النشاط حالياً.
        </div>

        @endforelse

    </div>

</div>


<style>
.card:hover {
    transform: scale(1.05);
    box-shadow: 0 3px 20px rgba(0,0,0,0.15);
}
</style>

@endsection
