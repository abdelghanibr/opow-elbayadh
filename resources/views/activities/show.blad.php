@extends('layouts.app')

@section('content')

<div class="container py-4" style="direction: rtl; text-align:right">

    <!-- ترويسة النشاط -->
    <div class="p-3 mb-4"
         style="background: linear-gradient(to right, #0a4f88, #0a8a67);
                border-radius: 10px; color: #fff; font-weight:600;">
        <h4 class="mb-0">
            <i class="fa-solid fa-person-running ms-2"></i>
            تفاصيل النشاط
        </h4>
    </div>

    <div class="card shadow-sm mb-5" style="border:2px solid {{ $activity->color }};">
        
        @if($activity->icon)
        <img src="{{ asset('uploads/activities/'.$activity->icon) }}"
             class="card-img-top"
             style="height:250px; object-fit:cover;">
        @endif

        <div class="card-body">

            <h4 class="fw-bold" style="color: {{ $activity->color }};">
                {{ $activity->title }}
            </h4>

            <p class="mt-2 text-muted">{{ $activity->description }}</p>

            <P class="fw-bold text-primary">
                🏷️ الفئة :
                <span class="text-dark">{{ $activity->category }}</span>
            </P>

            @if($activity->price)
            <p class="fw-bold" style="color:#0a4f88;">
                💰 السعر :
                <span class="text-success">{{ number_format($activity->price,2) }} دج</span>
            </p>
            @endif

            <!-- حالة النشاط -->
            @if(isset($activity->status))
            <p class="fw-bold">
                📌 الحالة :
                @if($activity->status == 'pending')
                    <span class="badge bg-warning text-dark">قيد التنفيذ</span>
                @else
                    <span class="badge bg-success">مكتملة</span>
                @endif
            </p>
            @endif

        </div>
    </div>


    <!-- قائمة المركبات التي تقدّم النشاط -->
    <h5 class="fw-bold mb-3 text-primary">
        🏟️ المركبات المتاحة لهذا النشاط:
    </h5>

    <div class="row g-4">

        @forelse($activity->complexes as $complex)
        <div class="col-md-4">

            <div class="card shadow-sm border">
                <div class="card-body">

                    <h5 class="fw-bold text-success">
                        {{ $complex->nom }}
                    </h5>

                    <p class="text-muted small">
                        <i class="fa-solid fa-users"></i>
                        السعة : {{ $complex->capacite }} شخص
                    </p>

                    <p class="fw-bold" style="color: #0a4f88;">
                        💵 سعر الحجز : 
                        <span class="text-success">
                            {{ number_format($complex->prix,2) }} دج
                        </span>
                    </p>

                    <a href="{{ route('reservation.form', $complex->id) }}" 
                       class="btn btn-success btn-sm">
                        <i class="fa-solid fa-pen-to-square ms-1"></i>
                        التسجيل في المركب
                    </a>

                </div>
            </div>

        </div>
        @empty

        <div class="alert alert-info text-center">
            ❌ لا توجد مركبات مرتبطة بهذا النشاط حالياً.
        </div>

        @endforelse

    </div>

</div>

@endsection
