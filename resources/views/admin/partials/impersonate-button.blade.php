{{-- Bouton « دخول إلى الحساب » : super admin uniquement. Usage : @include('admin.partials.impersonate-button', ['userId' => $x]) --}}
@if(!empty($userId) && \App\Services\ImpersonationService::isSuperAdmin(auth()->user()) && !\App\Services\ImpersonationService::isImpersonating() && (int) $userId !== (int) auth()->id())
    <form method="POST" action="{{ route('impersonate.start', $userId) }}" class="d-inline"
          onsubmit="return confirm('هل تريد الدخول إلى هذا الحساب ؟');">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-dark" title="دخول إلى هذا الحساب">
            <i class="fa-solid fa-right-to-bracket"></i> دخول إلى الحساب
        </button>
    </form>
@endif
