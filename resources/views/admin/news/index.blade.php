@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction:rtl;text-align:right">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>📰 الأخبار</h4>
        <a href="{{ route('news.create') }}" class="btn btn-success">
            ➕ خبر جديد
        </a>
    </div>

    {{-- Search --}}
    <input type="text"
           id="newsSearch"
           class="form-control js-date-fr mb-4"
           placeholder="🔍 ابحث عن خبر...">

    {{-- Cards --}}
    <div class="row g-4" id="newsCards">
        @forelse($news as $n)
        <div class="col-md-4 news-card">
            <div class="card h-100 shadow-sm">

                {{-- Image --}}
                @if($n->image)
                    <img src="{{ asset($n->image) }}"
                         class="card-img-top"
                         style="height:180px;object-fit:cover">
                @else
                    <div class="no-image">
                        <i class="fa-regular fa-image"></i>
                        لا توجد صورة
                    </div>
                @endif

                <div class="card-body">
                    <h6 class="fw-bold">{{ $n->title }}</h6>

                    <p class="text-muted small">
                        {{ Str::limit($n->content, 90) }}
                    </p>

                    <span class="badge {{ $n->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $n->is_active ? 'مفعل' : 'غير مفعل' }}
                    </span>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('news.edit', $n->id) }}"
                       class="btn btn-sm btn-primary">✏️</a>

                    <form method="POST"
                          action="{{ route('news.destroy', $n->id) }}"
                          onsubmit="return confirm('حذف الخبر؟')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">🗑️</button>
                    </form>
                </div>

            </div>
        </div>
        @empty
            <div class="alert alert-info text-center">
                لا توجد أخبار
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('js')
<script>
document.getElementById('newsSearch').addEventListener('keyup', function () {
    let value = this.value.toLowerCase();
    document.querySelectorAll('.news-card').forEach(card => {
        card.style.display =
            card.innerText.toLowerCase().includes(value)
            ? 'block'
            : 'none';
    });
});
</script>
@endpush
