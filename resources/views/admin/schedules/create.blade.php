@extends('layouts.app')

@php
    $src            = $source ?? null;
    $srcComplexId   = $src?->complexActivity?->complex_id;
    $srcActivityId  = $src?->complexActivity?->activity_id;
    $srcAllowsDup   = $src ? (bool) ($src->complexActivity->complex->allow_time_conflict ?? false) : false;

    // ⛔ المركب يمنع التداخل → تُنسخ الأوقات ليعدّلها المدير
    // ✅ المركب يسمح بالتداخل → لا تُنسخ الأوقات (المدير يختار توقيتات جديدة)
    $srcSlots       = ($src && ! $srcAllowsDup) ? ($src->time_slots ?? []) : [];
    if (is_string($srcSlots)) {
        $srcSlots = json_decode($srcSlots, true) ?: [];
    }
    $srcSlots       = is_array($srcSlots) ? array_values($srcSlots) : [];

    // خريطة: مركب → 1 إذا كان يسمح بتداخل الأوقات
    $complexConflictMap = $complexes->mapWithKeys(
        fn ($c) => [$c->id => $c->allowsTimeConflict() ? 1 : 0]
    );
@endphp

@section('content')
<div class="container py-5" style="direction: rtl; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11 col-12">

            <div class="card-modern shadow-lg border-0 rounded-4 overflow-hidden">
               
   <div class="card-header bg-gradient-warning text-white text-center py-4">
                     <h3 class="fw-bold mb-0">{{ $src ? '📋 نسخ فوج جديد' : 'إضافة فوج جديد' }}</h3>
                    <p class="small opacity-75 mt-2">حدد تفاصيل الجدول والأوقات الأسبوعية</p>
                </div>
                <div class="card-body p-4 p-md-5">
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if ($src)
    <div class="alert rounded-4 shadow-sm mb-4"
         style="background: linear-gradient(135deg, #e0f7fa, #cffafe); border:none;">
        <strong>📋 نسخة من الفوج: {{ $src->groupe }}</strong><br>
        <small class="text-muted">
            تم نسخ النشاط والفئة العمرية والأسعار ومدة الاشتراك داخل نفس المركب.
            <strong>غيّر اسم المجموعة</strong> و<strong>اختر المدرب</strong>، ثم راجع الأوقات.
        </small>
    </div>
@endif


                    <form action="{{ route('admin.schedules.store') }}" method="POST" id="scheduleForm">
                        @csrf

                        <div class="row g-4">

                            <!-- المركب -->
                            <div class="col-md-6">
                                <label for="complex" class="form-label fw-semibold text-primary">
                                    المركب <span class="text-danger">*</span>
                                </label>
                                <select name="complex_id" id="complex"
                                        class="form-select form-control-modern @error('complex_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- اختر المركب --</option>
                                    @foreach($complexes as $c)
                                        <option value="{{ $c->id }}" {{ old('complex_id', $srcComplexId) == $c->id ? 'selected' : '' }}>
                                            {{ $c->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('complex_id')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- النشاط -->
                            <div class="col-md-6">
                                <label for="activity" class="form-label fw-semibold text-primary">
                                    النشاط <span class="text-danger">*</span>
                                </label>
                                <select name="activity_id" id="activity"
                                        class="form-select form-control-modern @error('activity_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- اختر النشاط --</option>
                                    @foreach($activities as $a)
                                        <option value="{{ $a->id }}" {{ old('activity_id', $srcActivityId) == $a->id ? 'selected' : '' }}>
                                            {{ $a->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('activity_id')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" name="complex_activity_id" id="complex_activity_id">

                            <!-- الفئة العمرية -->
                            <div class="col-md-6">
                                <label for="age_category_id" class="form-label fw-semibold text-primary">
                                    الفئة العمرية <span class="text-danger">*</span>
                                </label>
                                <select name="age_category_id" id="age_category_id"
                                        class="form-select form-control-modern @error('age_category_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- اختر الفئة --</option>
                                    @foreach($ageCategories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('age_category_id', $src?->age_category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('age_category_id')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- المجموعة -->
                            <div class="col-md-6">
                                <label for="groupe" class="form-label fw-semibold text-primary">
                                    اسم المجموعة <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="groupe"
                                       id="groupe"
                                       value="{{ old('groupe', $src ? $src->groupe . ' - نسخة' : '') }}"
                                       class="form-control form-control-modern @error('groupe') is-invalid @enderror"
                                       placeholder="مثال: مجموعة الناشئين A"
                                       required>
                                @if ($src)
                                    <small class="text-muted">✏️ غيّر الاسم ليكون فوجاً مستقلاً.</small>
                                @endif
                                @error('groupe')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الجنس -->
                            <div class="col-md-6">
                                <label for="sex" class="form-label fw-semibold text-primary">
                                    الجنس
                                </label>
                                <select name="sex" id="sex"
                                        class="form-select form-control-modern @error('sex') is-invalid @enderror">
                                    <option value="X" {{ old('sex', $src?->sex ?? 'X') == 'X' ? 'selected' : '' }}>مختلط</option>
                                    <option value="H" {{ old('sex', $src?->sex ?? 'X') == 'H' ? 'selected' : '' }}>ذكور</option>
                                    <option value="F" {{ old('sex', $src?->sex ?? 'X') == 'F' ? 'selected' : '' }}>إناث</option>
                                </select>
                                @error('sex')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- عدد الأماكن -->
                            <div class="col-md-6">
                                <label for="nbr" class="form-label fw-semibold text-primary">
                                    عدد الأماكن المتاحة
                                </label>
                                <input type="number"
                                       name="nbr"
                                       id="nbr"
                                       value="{{ old('nbr', $src?->nbr) }}"
                                       min="1"
                                       class="form-control form-control-modern @error('nbr') is-invalid @enderror"
                                       placeholder="مثال: 20">
                                @error('nbr')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- نوع التسعيرة -->
                       

                            <!-- السعر الثابت (يظهر فقط إذا اختار سعر ثابت) -->
                            <div class="col-md-6" id="fixed_price_box">
                                <label for="price" class="form-label fw-semibold text-primary">
                                    السعر الثابت (دج)
                                </label>
                                <input type="number"
                                       name="price"
                                       id="price"
                                       value="{{ old('price', $src?->price) }}"
                                       step="0.01"
                                       min="0"
                                       class="form-control form-control-modern @error('price') is-invalid @enderror"
                                       placeholder="مثال: 1500">
                                @error('price')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="user_id" class="form-label fw-semibold text-primary">
                                    🔑 إسناد الجدول إلى مدرب (اختياري)
                                    @if ($src)
                                        <span class="badge bg-info text-dark">مطلوب للنسخة</span>
                                    @endif
                                </label>
                                <select name="user_id" id="user_id"
                                        class="form-select form-control-modern @error('user_id') is-invalid @enderror">
                                    <option value="">— لا أحد —</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}"
                                                {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }} ({{ $u->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                                     
<div class="card shadow-sm mb-4"
     style="
        border-radius: 18px;
        border: none;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
     ">

    <div class="card-body p-4">

        <h5 class="mb-4 fw-bold text-primary">
            ⚙️ إعدادات الاشتراك والمدة
        </h5>

        <div class="row">

            {{-- نوع الاشتراك --}}
            <div class="col-md-6 mb-3">
                <label class="fw-bold">📆 نوع الاشتراك</label>
                <select name="type_season"
                        class="form-select @error('type_season') is-invalid @enderror"
                        required>
                    @php $curSeason = old('type_season', $src?->type_season); @endphp
                    <option value="" disabled {{ $curSeason ? '' : 'selected' }}>— اختر نوع الاشتراك —</option>
                    <option value="session"  {{ $curSeason == 'session'  ? 'selected' : '' }}>حصة واحدة</option>
                    <option value="monthly"  {{ $curSeason == 'monthly'  ? 'selected' : '' }}>شهري</option>
                    <option value="season"   {{ $curSeason == 'season'   ? 'selected' : '' }}>موسمي</option>
                    <option value="ticket"   {{ $curSeason == 'ticket'   ? 'selected' : '' }}>تذكرة</option>
                </select>
                @error('type_season')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- التفعيل --}}
            <div class="col-md-6 mb-3">
                <label class="fw-bold d-block">🔌 الحالة</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input @error('active') is-invalid @enderror"
                           type="checkbox"
                           id="activeSwitch"
                           name="active"
                           value="1"
                           {{ old('active', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activeSwitch">
                        نشط
                    </label>
                    @error('active')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- تاريخ البداية --}}
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">📅 تاريخ البداية</label>
                <input type="text"
                       name="date_debut"
                       class="form-control js-date-fr js-date-fr @error('date_debut') is-invalid @enderror"
                           value="{{ old('date_debut', $src?->date_debut?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                @error('date_debut')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- تاريخ النهاية --}}
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">📅 تاريخ النهاية</label>
                <input type="text"
                       name="date_fin"
                       class="form-control js-date-fr js-date-fr @error('date_fin') is-invalid @enderror"
                     value="{{ old('date_fin', $src?->date_fin?->format('Y-m-d') ?? '9999-12-31') }}">
                @error('date_fin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>
    </div>
</div>



                        </div>

                        <!-- تعليمات التقويم -->
                        <div class="alert alert-soft-info rounded-4 p-3 text-center mt-4 shadow-sm">
                            <strong>كيفية اختيار الأوقات:</strong><br>
                            المنطقة الملوّنة = مواعيد مشغولة في المنشأة (كل نشاط بلونه الخاص)<br>
                            الأوقات الزرقاء = اختياراتك الحالية<br>
                            <em>انقر على الساعة لبدء الاختيار، ثم اسحب لتحديد المدة (ساعة واحدة)</em>
                        </div>

                        <div id="conflictNotice" class="alert rounded-4 p-3 text-center mt-3 shadow-sm d-none"
                             style="background: linear-gradient(135deg, #fef3c7, #fee2e2); border:none;">
                            <strong>⚠️ هذا المركب يسمح بتداخل الأوقات</strong><br>
                            يمكنك اختيار توقيت مشغول مسبقاً — لن يمنعك النظام من الحفظ.
                        </div>

                        <!-- التقويم -->
                        <input type="hidden" name="time_slots" id="time_slots">
                        <div class="card shadow-sm rounded-4 overflow-hidden mt-4">
                            <div class="card-body p-3">
                                <div id="calendar"></div>
                            </div>
                        </div>

                        <!-- زر الحفظ -->
                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg btn-glow w-100 w-sm-auto">
                                حفظ الجدول الجديد
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

{{-- ======================== STYLES 2026 ======================== --}}
@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">

<style>
    :root {
        --primary: #4361ee;
        --primary-gradient: linear-gradient(135deg, #4361ee, #4cc9f0);
        --warning-gradient: linear-gradient(135deg, #ffb302, #ffcc3d);
        --glass: rgba(255, 255, 255, 0.2);
        --shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        --border-glass: 1px solid rgba(255, 255, 255, 0.3);
    }
    body {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
    }
    .card-modern {
        background: var(--glass);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: var(--border-glass);
        box-shadow: var(--shadow);
    }
    .bg-gradient-warning {
        background: var(--warning-gradient);
    }
    .alert-soft-info {
        background: linear-gradient(135deg, #e0f7fa 0%, #cffafe 100%);
        border: none;
        backdrop-filter: blur(8px);
    }
    .form-control-modern,
    .form-select {
        background: rgba(255, 255, 255, 0.7);
        border: none;
        border-radius: 1rem;
        padding: 0.9rem 1.2rem;
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .form-control-modern:focus,
    .form-select:focus {
        background: white;
        transform: translateY(-3px);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.2);
    }
    .btn-glow-warning {
        background: var(--warning-gradient);
        color: white;
        transition: all 0.4s ease;
    }
    .btn-glow-warning:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(255, 179, 2, 0.4);
        color: white;
    }
    .selected-slot {
        background: #007bff !important;
        color: white !important;
        border: 2px solid #0056b3 !important;
        font-weight: bold;
        border-radius: 6px;
    }
    .fc-bg-event {
        opacity: 0.5 !important;
        border: none;
        border-radius: 4px;
    }
    .fc-timegrid-slot { height: 40px !important; }
    @media (max-width: 768px) { .fc-timegrid-slot { height: 35px !important; } }
</style>
@endpush

@push('js')

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
// 🚦 complexeId → 1 si ce complexe autorise le chevauchement d'horaires
const COMPLEX_ALLOWS_CONFLICT = {!! json_encode($complexConflictMap) !!};

@php
    $prefilledSlotsJson = json_encode(array_map(
        fn ($s) => [
            'day_number' => $s['day_number'] ?? null,
            'start'      => $s['start'] ?? null,
            'end'        => $s['end'] ?? null,
        ],
        $srcSlots
    ));
@endphp

let selectedSlots = [];
let slotSeq = 0;
let calendar;

function normalizeSlots(raw) {
    if (!Array.isArray(raw)) return [];
    return raw
        .map(s => ({
            day_number: Number(s.day_number),
            start: s.start,
            end: s.end
        }))
        .filter(s =>
            Number.isInteger(s.day_number) && s.day_number >= 0 && s.day_number <= 6 &&
            /^[01]\d:[0-5]\d$/.test(s.start || '') &&
            /^[01]\d:[0-5]\d$/.test(s.end || '') &&
            s.start < s.end
        );
}

(function initSlots() {
    try {
        selectedSlots = normalizeSlots(JSON.parse(@json($prefilledSlotsJson)));
    } catch (e) {
        selectedSlots = [];
    }
    selectedSlots = selectedSlots.map(s => ({ ...s, _id: 'sel-' + (slotSeq++) }));
})();

function updateHiddenField() {
    document.getElementById("time_slots").value = JSON.stringify(
        selectedSlots.map(s => ({ day_number: s.day_number, start: s.start, end: s.end }))
    );
}

function currentComplexId() {
    return document.getElementById("complex").value;
}

function conflictsAllowed() {
    return String(COMPLEX_ALLOWS_CONFLICT[currentComplexId()] || 0) === '1';
}

function refreshConflictNotice() {
    const notice = document.getElementById("conflictNotice");
    if (!notice) return;
    notice.classList.toggle('d-none', !conflictsAllowed());
}

function overlapMinutes(aStart, aEnd, bStart, bEnd) {
    return aStart < bEnd && aEnd > bStart;
}

document.addEventListener('DOMContentLoaded', function () {

    // 📅 Semaine fixe : dimanche → samedi de la semaine courante
    const now = new Date();
    const weekStart = new Date(now);
    weekStart.setDate(now.getDate() - now.getDay());
    weekStart.setHours(0, 0, 0, 0);
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekStart.getDate() + 7);

    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'timeGridWeek',
        locale: 'ar',
        direction: 'rtl',
        firstDay: 0,
        initialDate: weekStart,
        visibleRange: { start: weekStart, end: weekEnd },
        headerToolbar: { left: '', center: 'title', right: '' },
        selectable: true,
        selectOverlap: true,
        slotMinTime: "05:00:00",
        slotMaxTime: "23:00:00",
        slotDuration: "01:00:00",
        allDaySlot: false,
        expandRows: false,
        height: "auto",


eventDidMount(info) {
            if (info.event.display === 'background') {

                const groupName = info.event.extendedProps?.groupe;
                const actName = info.event.extendedProps?.activity;
                if (!groupName) return;

                const label = document.createElement('div');
                label.innerText = (actName ? actName + ' · ' : '') + groupName;

                label.style.position = 'absolute';
                label.style.top = '0';
                label.style.left = '0';
                label.style.right = '0';
                label.style.bottom = '0';
                label.style.display = 'flex';
                label.style.alignItems = 'center';
                label.style.justifyContent = 'center';
                label.style.textAlign = 'center';
                label.style.fontSize = '9px';
                label.style.fontWeight = 'bold';
                label.style.color = '#fff';
                label.style.whiteSpace = 'normal';
                label.style.wordBreak = 'break-word';
                label.style.overflow = 'hidden';
                label.style.lineHeight = '1.1';
                label.style.padding = '2px';
                label.style.borderRadius = '4px';
                label.style.background = info.event.backgroundColor || '#dc3545';

                info.el.appendChild(label);
            }
        },
        select(info) {

         


            const day   = info.start.getDay();
            const start = info.startStr.slice(11, 16);
            const end   = info.endStr.slice(11, 16);

            // 🚦 منع الاختيار حسب إعداد المركب
            if (!conflictsAllowed()) {

                const busy = calendar.getEvents().some(ev =>
                    ev.display === 'background' &&
                    info.start < ev.end &&
                    info.end > ev.start
                );

                if (busy) {
                    alert('⛔ هذا التوقيت مشغول مسبقاً في هذا المركب');
                    calendar.unselect();
                    return;
                }

                // ⛔ لا تداخل بين أوقات الفوج نفسه
                const selfOverlap = selectedSlots.some(s =>
                    s.day_number === day && overlapMinutes(s.start, s.end, start, end)
                );

                if (selfOverlap) {
                    alert('⛔ هذا التوقيت يتداخل مع توقيت اخترته بالفعل في هذا الفوج');
                    calendar.unselect();
                    return;
                }
            }

            const slot = { _id: 'sel-' + (slotSeq++), day_number: day, start, end };

            selectedSlots.push(slot);

            calendar.addEvent({
                id: slot._id,
                start: info.start,
                end: info.end,
                classNames: ['selected-slot'],
                title: 'توقيت مختار'
            });

            updateHiddenField();
            calendar.unselect();
        },

        eventClick(info) {
            if (!info.event.classNames.includes('selected-slot')) return;

            selectedSlots = selectedSlots.filter(s => s._id !== info.event.id);
            info.event.remove();
            updateHiddenField();
        }
    });

    // 🟦 رسم الفترات المسبقة المعبّأة (وضع النسخ)
    selectedSlots.forEach(slot => {
        const dayDate = new Date(weekStart);
        dayDate.setDate(weekStart.getDate() + slot.day_number);

        const dayStr = dayDate.getFullYear() + '-' +
            String(dayDate.getMonth() + 1).padStart(2, '0') + '-' +
            String(dayDate.getDate()).padStart(2, '0');

        calendar.addEvent({
            id: slot._id,
            start: dayStr + 'T' + slot.start + ':00',
            end: dayStr + 'T' + slot.end + ':00',
            classNames: ['selected-slot'],
            title: 'توقيت منسوخ'
        });
    });

    updateHiddenField();
    refreshConflictNotice();
    calendar.render();

    // ✅ تحميل فوري إذا كان المركب معبّأ مسبقاً (وضع النسخ أو بعد خطأ تحقق)
    if (currentComplexId()) {
        loadOccupied();
    }
});

// ===============================
// تحميل الأوقات المشغولة تلقائياً (كل النشاطات الملوّنة)
// ===============================
document.getElementById("complex").addEventListener("change", loadOccupied);
document.getElementById("activity").addEventListener("change", loadOccupied);

function loadOccupied() {

    const complex = currentComplexId();

    refreshConflictNotice();

    // 🧹 حذف الأحداث المشغولة السابقة فقط (لا نلمس الفترات الزرقاء)
    calendar.getEvents().forEach(e => {
        if (e.display === 'background') e.remove();
    });

    if (!complex) return;

    fetch(`{{ route('admin.schedules.occupied') }}?complex_id=${complex}`)
        .then(res => res.json())
        .then(events => {
            events.forEach(ev => {
                calendar.addEvent(ev);
            });
        });
}

// ===============================
// 🚫 لا حفظ بدون أي توقيت
// ===============================
document.getElementById("scheduleForm").addEventListener("submit", function (e) {
    if (selectedSlots.length === 0) {
        e.preventDefault();
        alert('❌ اختر توقيتاً واحداً على الأقل من التقويم قبل الحفظ');
    }
});
</script>


@endpush
