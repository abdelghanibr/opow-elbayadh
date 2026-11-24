{{-- Sélecteur « Basculer vers… » (super admin) : liste chargée à la demande (JSON) --}}
<div class="dropdown" id="impSwitcher">
    <button class="btn btn-warning btn-nav dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <i class="fa-solid fa-user-secret"></i> التبديل إلى حساب…
    </button>
    <div class="dropdown-menu p-2" style="min-width:320px; max-height:420px; overflow:auto">
        <input type="search" class="form-control form-control-sm mb-2" id="impSearch"
               placeholder="الاسم، البريد الإلكتروني، النوع، المجمع…" autocomplete="off">
        <div id="impResults" class="small text-muted px-1">اكتب للبحث…</div>
    </div>
</div>

<form id="impForm" method="POST" style="display:none">@csrf</form>

<script>
(function () {
    const box = document.getElementById('impSwitcher');
    if (!box) return;
    const input = document.getElementById('impSearch');
    const out = document.getElementById('impResults');
    const form = document.getElementById('impForm');
    let timer = null, loaded = false;

    function esc(s) { return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

    function load() {
        out.textContent = 'جارٍ التحميل…';
        fetch(@json(route('impersonate.users')) + '?q=' + encodeURIComponent(input.value.trim()), {
            headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'
        }).then(r => r.json()).then(rows => {
            if (!rows.length) { out.textContent = 'لا توجد نتائج'; return; }
            out.innerHTML = rows.map(u =>
                '<button type="button" class="dropdown-item text-start" data-id="' + u.id + '" data-label="' + esc(u.name || u.email) + '">'
                + '<div class="fw-semibold">' + esc(u.name || '—') + '</div>'
                + '<div class="text-muted" style="font-size:12px">' + esc(u.email) + ' · ' + esc(u.type) + (u.complex_name ? ' · ' + esc(u.complex_name) : '') + '</div>'
                + '</button>').join('');
        }).catch(() => { out.textContent = 'خطأ في التحميل'; });
    }

    box.addEventListener('show.bs.dropdown', () => { if (!loaded) { loaded = true; load(); } });
    input.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(load, 300); });
    out.addEventListener('click', e => {
        const b = e.target.closest('button[data-id]');
        if (!b) return;
        if (!confirm('هل تريد الدخول إلى حساب : ' + b.dataset.label + ' ؟')) return;
        form.action = @json(url('/admin/impersonate')) + '/' + b.dataset.id;
        form.submit();
    });
})();
</script>
