@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction:rtl; text-align:right">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">📅 الفعاليات</h4>
        <a href="{{ route('events.create') }}" class="btn btn-success rounded-pill px-4">
            ➕ فعالية جديدة
        </a>
    </div>

    {{-- SEARCH --}}
    <input type="text"
           id="eventSearch"
           class="form-control mb-4 rounded-pill px-4"
           placeholder="🔍 ابحث عن فعالية...">

    {{-- CARDS --}}
    <div class="row g-4" id="eventCards">

        @forelse($events as $e)
        <div class="col-12 col-md-6 col-lg-4 event-card">
            <div class="event-card-modern position-relative h-100">

                {{-- STATUS --}}
                <span class="status-badge {{ $e->is_active ? 'active' : 'inactive' }}">
                    {{ $e->is_active ? 'مفعل' : 'غير مفعل' }}
                </span>

                {{-- IMAGE --}}
                @if($e->image)
                    <div class="event-image-wrapper">
                        <img src="{{ asset($e->image) }}" alt="{{ $e->title }}">
                    </div>
                @else
                    <div class="event-image-placeholder">
                        <i class="fa-regular fa-calendar"></i>
                        <span>بدون صورة</span>
                    </div>
                @endif

                {{-- BODY --}}
                <div class="event-body">
                    <h6 class="fw-bold mb-2">{{ $e->title }}</h6>
                    <p class="text-muted small mb-2">
                        {{ \Illuminate\Support\Str::limit($e->description, 90) }}
                    </p>
                    <div class="small text-muted">
                        📅 {{ $e->start_date }} → {{ $e->end_date }}
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="event-footer">
                    <a href="{{ route('events.edit', $e->id) }}"
                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        ✏️ تعديل
                    </a>

                    <div class="d-flex gap-2">

                        {{-- Facebook Share --}}
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('events.show',$e->id)) }}"
                           target="_blank"
                           class="btn-facebook-icon"
                           title="مشاركة على فيسبوك">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>

                        {{-- Delete --}}
                        <form method="POST"
                              action="{{ route('events.destroy', $e->id) }}"
                              onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                🗑
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    لا توجد فعاليات
                </div>
            </div>
        @endforelse

    </div>
</div>
@endsection

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<style>
.event-card-modern{
    background:#fff;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 14px 34px rgba(0,0,0,.08);
    transition:.35s;
    display:flex;
    flex-direction:column;
}
.event-card-modern:hover{
    transform:translateY(-8px);
    box-shadow:0 22px 46px rgba(0,0,0,.15);
}

/* STATUS */
.status-badge{
    position:absolute;
    top:16px;
    left:16px;
    padding:6px 14px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
    z-index:3;
}
.status-badge.active{ background:#16a34a;color:#fff }
.status-badge.inactive{ background:#9ca3af;color:#fff }

/* IMAGE */
.event-image-wrapper{
    height:210px;
    overflow:hidden;
}
.event-image-wrapper img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:.4s;
}
.event-card-modern:hover img{
    transform:scale(1.1);
}
.event-image-placeholder{
    height:210px;
    background:#f1f5f9;
    color:#64748b;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    gap:6px;
}

/* BODY */
.event-body{
    padding:18px 20px;
    flex:1;
}

/* FOOTER */
.event-footer{
    padding:14px 18px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-top:1px solid #e5e7eb;
    background:#fafafa;
}

/* Facebook Icon Button */
.btn-facebook-icon{
    width:38px;
    height:38px;
    border-radius:50%;
    background:#1877f2;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:.3s;
    box-shadow:0 6px 16px rgba(24,119,242,.3);
}
.btn-facebook-icon:hover{
    background:#166fe5;
    transform:translateY(-3px) scale(1.05);
    box-shadow:0 10px 26px rgba(24,119,242,.45);
}

/* Responsive */
@media(max-width:576px){
    .event-image-wrapper,
    .event-image-placeholder{ height:180px }
}
</style>


{{-- ================= JS SEARCH ================= --}}
<script>
document.getElementById('eventSearch').addEventListener('keyup', function () {
    const value = this.value.toLowerCase().trim();
    document.querySelectorAll('.event-card').forEach(card => {
        card.style.display =
            card.innerText.toLowerCase().includes(value)
            ? 'block'
            : 'none';
    });
});
</script>
