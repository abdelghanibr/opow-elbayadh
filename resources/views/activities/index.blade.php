@extends('layouts.app')
@section('content')

<div class="container py-4" style="direction: rtl; text-align:right">

    <!-- 🟦 HEADER -->
    <div class="p-3 mb-4 activity-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>إدارة جميع النشاطات الخاصة بك هنا</span>
            <span class="fs-6"><i class="fa-solid fa-wave-pulse"></i> نشاطاتي</span>
        </div>
    </div>

    {{-- 🔍 SEARCH + CATEGORY --}}
    <form method="GET" action="{{ route('activities.index') }}" class="row g-2 mb-4">
        <div class="col-12 col-md-6">
            <input name="search" id="searchInput" type="text"
                   class="form-control form-control-sm"
                   value="{{ request('search') }}" placeholder="ابحث عن نشاط...">
        </div>

        <div class="col-12 col-md-6">
            <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">كل الفئات</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <!-- 🧩 MINI CARDS -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">

        @forelse ($activities as $a)
        <div class="col">

            <div class="card activity-mini-card shadow-sm h-100 bg-white text-center"
                 style="border-radius: 16px; border: none;">

                <!-- CERCLE AVEC ICONE -->
                <div class="activity-img-circle mx-auto mt-3">
                    @if($a->icon)
                        <img src="{{ $a->icon }}"
                             class="rounded-circle w-100 h-100"
                             style="object-fit: cover;"
                             onerror="this.src='{{ asset('images/default-activity.png') }}'">
                    @else
                        <div class="placeholder-circle d-flex align-items-center justify-content-center rounded-circle">
                            <i class="fa-regular fa-image fa-2x text-muted"></i>
                        </div>
                    @endif
                </div>

                <div class="card-body d-flex flex-column py-3 px-2">

                    <h6 class="fw-bold mb-1 small" style="color: {{ $a->color }};">
                        {{ $a->title }}
                    </h6>

                    <span class="badge bg-primary mb-2 small">
                        {{ $a->activityCategory->name ?? 'بدون فئة' }}
                    </span>

                    <p class="text-muted small mb-2 flex-grow-1" style="font-size: 0.75rem;">
                        {{ Str::limit($a->description, 60) }}
                    </p>

                    <!-- 🔥 BUTTON WITH JS -->
                    <button onclick="selectActivity({{ $a->id }});"
                            class="btn btn-success btn-sm mt-auto w-100">
                        تسجيل
                    </button>

                </div>
            </div>

        </div>
        @empty

        <div class="col-12">
            <div class="alert alert-info text-center">لا توجد نشاطات متاحة حالياً.</div>
        </div>

        @endforelse

    </div>
</div>
@endsection

@push('css')
<style>
    .activity-header {
        background: linear-gradient(to right, #0a4f88, #0a8a67);
        border-radius: 14px;
        color: #fff;
        font-weight: 700;
    }

    .activity-mini-card {
        transition: 0.3s ease;
        cursor: pointer;
    }

    .activity-mini-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,.15);
    }

    .activity-img-circle {
        width: 80px;
        height: 80px;
        padding: 5px;
        border-radius: 50%;
        background: linear-gradient(45deg, #ffd700, #f9c74f, #ffd700);
        background-size: 200% 200%;
        animation: shine 4s linear infinite;
        box-shadow: 0 6px 20px rgba(255,215,0,0.3);
    }

    .placeholder-circle i { color: #999; }

    @keyframes shine {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
</style>
@endpush

@push('js')
<script>
/* Auto search */
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    let timer = null;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => input.form.submit(), 500);
    });
});

/* 🔥 selectActivity via AJAX */
function selectActivity(activityId) {
    fetch("{{ route('activities.select') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ activity_id: activityId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = "{{ route('reservation.form', auth()->user()->complex_id) }}";
        }
    })
    .catch(err => console.error("Erreur:", err));
}
</script>
@endpush
