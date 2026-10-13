@extends('layouts.app')

@section('content')

<div class="container py-4" style="direction: rtl; text-align:right;">

    {{-- ===== العنوان + زر الإضافة ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">👥 قائمة عمال / أعضاء المؤسسة</h5>

        <div>
        <a href="{{ route('entreprise.cards') }}" class="btn btn-success btn-sm">
            🪪 طباعة كل البطاقات
        </a>

        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importPersonsModal">
            📥 استيراد بالجملة (Excel)
        </button>

        <a href="{{ route('entreprise.persons.create') }}"
           class="btn btn-success btn-sm">
            ➕ إضافة شخص
        </a>
        </div>
    </div>

    {{-- ===== رسالة نجاح ===== --}}
    @if(session('success'))
        <div class="alert alert-success text-center fw-bold">
            {{ session('success') }}
        </div>
    @endif

    {{-- ===== نتيجة الاستيراد ===== --}}
    @if(session('import_blocked'))
        <div class="alert alert-danger">
            <div class="fw-bold mb-2">🚫 لم يتم الاستيراد: الملف يحتوي على أخطاء أو تكرارات. يرجى تصحيح الملف ثم إعادة المحاولة.</div>
            <ul class="mb-0">
                @foreach(session('import_errors', []) as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @else
        @if(session('import_success_count') !== null)
            <div class="alert alert-success text-center fw-bold">
                ✅ تم استيراد {{ session('import_success_count') }} شخص بنجاح.
            </div>
        @endif

        @if(session('import_errors') && count(session('import_errors')))
            <div class="alert alert-danger">
                <div class="fw-bold mb-2">⚠️ لم يتم استيراد بعض الأسطر:</div>
                <ul class="mb-0">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>⚠ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===== الجدول ===== --}}
    <div class="table-responsive">
        <table id="personsTable"
               class="table table-striped table-bordered align-middle text-center w-100">

            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>الصورة</th>
                    <th>الاسم</th>
                    <th>اللقب</th>
                    <th>العمر</th>
                    <th>الجنس</th>
                    <th>الوظيفة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>

            <tbody>
            @foreach($persons as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    {{-- ===== الصورة ===== --}}
                    <td>
                        @if($p->photo)
                            <img src="{{ asset($p->photo) }}"
                                 alt="photo"
                                 class="person-avatar"
                                 data-bs-toggle="modal"
                                 data-bs-target="#photoModal{{ $p->id }}">
                        @else
                            <img src="{{ asset('images/avatar-default.png') }}"
                                 class="person-avatar">
                        @endif
                    </td>

                    <td>{{ $p->firstname }}</td>
                    <td>{{ $p->lastname }}</td>

                    <td>
                        @if($p->birth_date)
                            {{ \Carbon\Carbon::parse($p->birth_date)->age }} سنة
                        @else
                            —
                        @endif
                    </td>

                    <td>{{ $p->gender ?? '—' }}</td>
                    <td>{{ $p->education ?? '—' }}</td>

                    {{-- ===== الإجراءات ===== --}}
                    <td>
                        <a href="{{ route('entreprise.persons.edit', $p->id) }}"
                           class="btn btn-sm btn-warning">
                            ✏ تعديل
                        </a>

                        <a href="{{ route('entreprise.card', $p->id) }}"
                           target="_blank"
                           class="btn btn-sm btn-primary">
                            🪪 بطاقة
                        </a>

                        <form action="{{ route('entreprise.persons.delete', $p->id) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('⚠ هل أنت متأكد من الحذف؟');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                🗑 حذف
                            </button>
                        </form>
                    </td>
                </tr>

                {{-- ===== Modal معاينة الصورة ===== --}}
                @if($p->photo)
                <div class="modal fade" id="photoModal{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <img src="{{ asset($p->photo) }}"
                                     class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            @endforeach
            </tbody>
        </table>
    </div>

    {{-- ===== Modal الاستيراد بالجملة ===== --}}
    <div class="modal fade" id="importPersonsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="direction: rtl; text-align:right;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">📥 استيراد الأشخاص بالجملة (Excel)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <ol class="mb-3">
                        <li>
                            حمّل النموذج:
                            <a href="{{ route('entreprise.persons.import.template') }}" class="btn btn-outline-success btn-sm">
                                ⬇️ تحميل النموذج (modele_import_personnes.xlsx)
                            </a>
                        </li>
                        <li>املأ أسطر الأشخاص في الملف دون تغيير ترتيب الأعمدة.</li>
                        <li>ارفع الملف عبر النموذج أسفله.</li>
                    </ol>

                    <div class="alert alert-light border">
                        <div class="fw-bold mb-1">ترتيب الأعمدة (5 أعمدة):</div>
                        <code>الاسم | اللقب | تاريخ الميلاد | الجنس | التصنيف</code>
                        <ul class="mb-0 mt-2 small">
                            <li><b>تاريخ الميلاد</b>: بصيغة <code>YYYY-MM-DD</code> (مثال: <code>2015-05-04</code>)</li>
                            <li><b>الجنس</b>: <code>ذكر</code> أو <code>أنثى</code></li>
                            <li><b>التصنيف</b>: لاعب / مدرب / مسير / آخر</li>
                        </ul>
                    </div>

                    <form id="importPersonsForm" action="{{ route('entreprise.persons.import') }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf

                        <label class="form-label fw-bold">ملف الأشخاص (xlsx أو csv)</label>
                        <input type="file" name="file" id="import_persons_file"
                               class="form-control" accept=".xlsx,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">

                        <div class="form-text text-muted">
                            الصيغ المسموحة: xlsx / csv — الحجم الأقصى 2 ميغابايت.
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" form="importPersonsForm" class="btn btn-primary">📥 رفع واستيراد</button>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
@push('css')
<style>
.person-avatar {
    width: 45px;
    height: 45px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    transition: transform .2s ease;
}

.person-avatar:hover {
    transform: scale(1.08);
}
</style>
@endpush
@push('js')
@include('admin.partials.datatable-script', ['tableId' => '#personsTable'])

<script>
(function () {
    var form  = document.getElementById('importPersonsForm');
    var input = document.getElementById('import_persons_file');
    if (!form || !input) return;

    function errorEl() {
        var el = input.parentNode.querySelector('.js-import-error');
        if (!el) {
            el = document.createElement('div');
            el.className = 'text-danger small mt-1 js-import-error';
            input.parentNode.appendChild(el);
        }
        return el;
    }

    function validate() {
        var file = input.files && input.files[0];
        var err  = errorEl();

        if (!file) {
            err.textContent = 'يرجى اختيار ملف Excel (xlsx) أو CSV.';
            input.classList.add('is-invalid');
            return false;
        }

        var ext = (file.name.split('.').pop() || '').toLowerCase();
        if (['xlsx', 'csv', 'txt'].indexOf(ext) === -1) {
            err.textContent = 'الصيغة غير مدعومة. المسموح: xlsx أو csv.';
            input.classList.add('is-invalid');
            return false;
        }

        if (file.size > 2 * 1024 * 1024) {
            err.textContent = 'حجم الملف يتجاوز 2 ميغابايت.';
            input.classList.add('is-invalid');
            return false;
        }

        err.remove();
        input.classList.remove('is-invalid');
        return true;
    }

    input.addEventListener('change', validate);

    form.addEventListener('submit', function (e) {
        if (!validate()) {
            e.preventDefault();
        }
    });
})();
</script>
@endpush
