@extends('layouts.app')

@section('content')

<style>
    .card-select {
        background: linear-gradient(135deg, #4f46e5, #6d28d9);
        border-radius: 18px;
        padding: 25px;
        color: white;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        transition: 0.25s ease-in-out;
        cursor: pointer;
    }

    .card-select:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.25);
    }

    .icon-type {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .search-box {
        background: white;
        border-radius: 12px;
        padding: 12px 18px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
</style>

<div class="container py-4">

    <h2 class="text-center mb-4">اختر نوع المركّب الرياضي</h2>

    <!-- مربع البحث -->
    <div class="search-box">
        <input type="text" id="searchInput" class="form-control"
               placeholder="🔍 بحث عن نوع المركّب…">
    </div>

    <div class="row g-4" id="typesContainer">

        @foreach ($types as $type)
            <div class="col-md-4 type-card">
                <a href="{{ route('reservation.list_by_type', $type) }}" style="text-decoration:none;">
                    <div class="card-select text-center">

                        @php
                            $icons = [
                                'كرة القدم'      => 'fa-futbol',
                                'رياضات جماعية'  => 'fa-people-group',
                                'لياقة بدنية'    => 'fa-dumbbell',
                                'فنون قتالية'    => 'fa-hand-fist',
                                'مسبح'           => 'fa-water-ladder',
                                'stad'           => 'fa-building',
                                'stadx'          => 'fa-building-columns',
                            ];
                        @endphp

                        <i class="fa-solid {{ $icons[$type] ?? 'fa-circle' }} icon-type"></i>

                        <h4>{{ $type }}</h4>
                    </div>
                </a>
            </div>
        @endforeach

    </div>

</div>

<script>
    const searchInput = document.getElementById("searchInput");
    const cards = document.querySelectorAll(".type-card");

    searchInput.addEventListener("keyup", function() {
        const value = this.value.toLowerCase();

        cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(value) ? "" : "none";
        });
    });
</script>

@endsection
