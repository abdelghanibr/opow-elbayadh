{{-- Messages de l'administration (notifications) : non lus en évidence, bouton « تم الاطلاع » --}}
@php
    // $inboxAll = true (profil) : historique complet, du plus récent au plus ancien ; sinon notifications (lu > 24 h masqué)
    $inboxMessages = \App\Models\UserMessage::latestFor(auth()->id(), $inboxAll ?? false ? 100 : 20, (bool) ($inboxAll ?? false));
    $inboxUnread = $inboxMessages->whereNull('read_at')->count();
@endphp
@if($inboxMessages->count())
<div class="{{ ($inboxBare ?? false) ? '' : 'card mb-4 border-0 shadow-sm' }}" id="messages-admin" style="direction: rtl; text-align: right;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>📩 رسائل الإدارة
            @if($inboxUnread)<span class="badge bg-danger">{{ $inboxUnread }}</span>@endif
        </strong>
        @if($inboxUnread)
            <form method="POST" action="{{ route('messages.read-all') }}">@csrf
                <button class="btn btn-sm btn-outline-secondary">تحديد الكل كمقروء</button>
            </form>
        @endif
    </div>
    <div class="list-group list-group-flush">
        @foreach($inboxMessages as $m)
            <div class="list-group-item {{ $m->read_at ? '' : 'bg-warning bg-opacity-10' }}">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="fw-bold">{{ $m->title }} @unless($m->read_at)<span class="badge bg-danger">جديد</span>@endunless</div>
                        <div style="white-space: pre-line;">{{ $m->body }}</div>
                        <small class="text-muted">{{ $m->sent_by ?: 'الإدارة' }} — {{ $m->created_at?->format('Y-m-d H:i') }}</small>
                    </div>
                    @unless($m->read_at)
                        <form method="POST" action="{{ route('messages.read', $m->id) }}">@csrf
                            <button class="btn btn-sm btn-outline-primary">تم الاطلاع</button>
                        </form>
                    @endunless
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
