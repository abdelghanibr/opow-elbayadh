{{-- Carte « رسائل الإدارة » du tableau de bord : badge des non lus + fenêtre (popup) avec l'historique, le plus récent d'abord --}}
@php $cardUnread = \App\Models\UserMessage::unreadCount(auth()->id()); @endphp
<div class="col-md-4">
    <div class="dash-card">
        <h5>📩 رسائل الإدارة
            @if($cardUnread)<span class="badge bg-danger rounded-pill">{{ $cardUnread }}</span>@endif
        </h5>
        <p class="text-muted">إشعارات ورسائل من إدارة المنشأة</p>
        <button type="button" class="btn btn-main btn-sm" data-bs-toggle="modal" data-bs-target="#adminMessagesModal">
            عرض الرسائل
        </button>
    </div>
</div>

<div class="modal fade" id="adminMessagesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content" style="direction: rtl; text-align: right;">
            <div class="modal-header">
                <h5 class="modal-title">📩 رسائل الإدارة</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                @include('messages.partials.inbox', ['inboxAll' => true, 'inboxBare' => true])
                @if(\App\Models\UserMessage::latestFor(auth()->id(), 1, true)->isEmpty())
                    <p class="text-center text-muted mb-0">لا توجد رسائل.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if(request()->query('messages') || session('open_messages'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('adminMessagesModal');
        if (el && window.bootstrap) { new bootstrap.Modal(el).show(); }
    });
</script>
@endif
