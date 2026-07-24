@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11 col-12">

            <div class="card-modern shadow-lg border-0 rounded-4 overflow-hidden">
               
   <div class="card-header bg-gradient-warning text-white text-center py-4">
                     <h3 class="fw-bold mb-0">إضافة فوج جديد</h3>
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
                                        <option value="{{ $c->id }}" {{ old('complex_id') == $c->id ? 'selected' : '' }}>
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
                                        <option value="{{ $a->id }}" {{ old('activity_id') == $a->id ? 'selected' : '' }}>
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
                                        <option value="{{ $cat->id }}" {{ old('age_category_id') == $cat->id ? 'selected' : '' }}>
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
                                       value="{{ old('groupe') }}"
                                       class="form-control form-control-modern @error('groupe') is-invalid @enderror"
                                       placeholder="مثال: مجموعة الناشئين A"
                                       required>
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
                                    <option value="X" {{ old('sex', 'X') == 'X' ? 'selected' : '' }}>مختلط</option>
                                    <option value="H" {{ old('sex') == 'H' ? 'selected' : '' }}>ذكور</option>
                                    <option value="F" {{ old('sex') == 'F' ? 'selected' : '' }}>إناث</option>
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
                                       value="{{ old('nbr') }}"
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
                                       value="{{ old('price') }}"
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
                    <option value="" disabled selected>— اختر نوع الاشتراك —</option>
                    <option value="session">حصة واحدة</option>
                   
                    <option value="monthly">شهري</option>
                  
                   
                    <option value="season">موسمي</option>
                    <option value="ticket">تذكرة</option>
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
                          value="{{ old('date_debut', now()->format('Y-m-d')) }}">
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
                     value="{{ old('date_fin', '9999-12-31') }}">
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
                            الأوقات الحمراء = مشغولة مسبقًا<br>
                            الأوقات الزرقاء = اختياراتك الحالية<br>
                            <em>انقر على الساعة لبدء الاختيار، ثم اسحب لتحديد المدة (ساعة واحدة)</em>
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
        background-color: #dc3545 !important;
        opacity: 0.55 !important;
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
let selectedSlots = [];
let calendar;

function updateHiddenField() {
    document.getElementById("time_slots").value = JSON.stringify(selectedSlots);
}

document.addEventListener('DOMContentLoaded', function () {

    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'timeGridWeek',
        locale: 'ar',
        direction: 'rtl',
        firstDay: 0,
        selectable: true,
        selectOverlap: false, // ❌ يمنع التداخل
        slotMinTime: "05:00:00",
        slotMaxTime: "23:00:00",
        slotDuration: "01:00:00",
        allDaySlot: false,
        expandRows: false,
        height: "auto",


eventDidMount(info) {
            if (info.event.display === 'background') {

                const groupName = info.event.extendedProps?.groupe;
                if (!groupName) return;

                const label = document.createElement('div');
                label.innerText = groupName;

                label.style.position = 'absolute';
                label.style.top = '50%';
                label.style.left = '50%';
                label.style.transform = 'translate(-50%, -50%)';
                label.style.fontSize = '8px';
                label.style.fontWeight = 'bold';
         label.style.color = '#000';        // noir
label.style.textShadow = 'none';   // اختياري
                label.style.whiteSpace = 'nowrap';
                label.style.opacity = '3';
                label.style.textShadow = '0 1px 2px rgba(0,0,0,.6)';

                info.el.appendChild(label);
            }
        },
        select(info) {

         


            // ❌ منع اختيار وقت مشغول
            const conflict = calendar.getEvents().some(ev =>
                ev.display === 'background' &&
                info.start < ev.end &&
                info.end > ev.start
            );

            if (conflict) {
                alert('⛔ هذا التوقيت مشغول مسبقاً');
                calendar.unselect();
                return;
            }

            const slot = {
                day_number: new Date(info.start).getDay(),
                start: info.startStr.slice(11,16),
                end:   info.endStr.slice(11,16)
            };

            selectedSlots.push(slot);

            calendar.addEvent({
                start: info.start,
                end: info.end,
                classNames: ['selected-slot'],
                title: 'توقيت مختار'
            });

            updateHiddenField();
            calendar.unselect();
        },

        eventClick(info) {
            if (info.event.display === 'background') return;

            selectedSlots = selectedSlots.filter(s =>
                s.start !== info.event.startStr.slice(11,16)
            );
            info.event.remove();
            updateHiddenField();
        }
    });

    calendar.render();
});

// ===============================
// تحميل الأوقات المشغولة تلقائياً
// ===============================
document.getElementById("complex").addEventListener("change", loadOccupied);
document.getElementById("activity").addEventListener("change", loadOccupied);

function loadOccupied() {

    const complex = document.getElementById("complex").value;
    const activity = document.getElementById("activity").value;

    if (!complex || !activity) return;

    // 🧹 حذف الأحداث السابقة
    calendar.getEvents().forEach(e => e.remove());

    fetch(`{{ route('admin.schedules.occupied') }}?complex_id=${complex}&activity_id=${activity}`)
        .then(res => res.json())
        .then(events => {

            events.forEach(ev => {
                calendar.addEvent(ev);
            });

        });
}
</script>


@endpush
