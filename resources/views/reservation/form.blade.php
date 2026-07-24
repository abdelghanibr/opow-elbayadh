@extends('layouts.app')

@section('content')
<div class="container py-4 py-md-5" style="direction: rtl; text-align: right;">
    @php
        $typeLabel = match (auth()->user()->type) {
            'person' => 'فرد',
            'club' => 'نادي',
            'company' => 'مؤسسة',
            default => 'مستخدم'
        };
        $ageCategoryName = optional(optional(auth()->user()->person)->ageCategory)->name;
        $hasSchedules = $schedules->isNotEmpty();

        $daysMap = [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];

        $seasonLabels = [
            'session' => 'جلسة',
            'weekly' => 'أسبوعي',
            'monthly' => 'شهري',
            'quarterly' => 'ثلاثي',
            'semester' => 'سداسي',
            'season' => 'موسمي',
            'ticket' => 'تذكرة',
        ];
    @endphp

    {{-- رسائل النجاح والأخطاء --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center fw-bold" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show text-center fw-bold" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show fw-bold" role="alert">
            <ul class="mb-0 list-unstyled">
                @foreach($errors->all() as $error)
                    <li>⚠ {{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('reservation.store') }}" method="POST" id="reserveForm">
        @csrf
        <input type="hidden" name="complex_activity_id" value="{{ $complexActivity->id }}">
        <input type="hidden" name="schedule_id" id="schedule_id">
        <input type="hidden" name="season_id" id="season_id" value="{{ $selectedSeasonId }}">

        {{-- 🧾 معلومات الحجز --}}
        <div class="card shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-body p-4">
                <h5 class="fw-bold text-primary mb-4">🔹 معلومات الحجز</h5>
                <div class="row g-3 g-md-4">
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">👤 اسم المستخدم</label>
                        <input class="form-control bg-light" value="{{ auth()->user()->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">🏷️ نوع المستخدم</label>
                        <input class="form-control bg-light" value="{{ $typeLabel }}" readonly>
                    </div>

                    @if(auth()->user()->type === 'person')
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">🎯 الفئة العمرية</label>
                            <input class="form-control bg-light" value="{{ $ageCategoryName ?? 'غير محدد' }}" readonly>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">🏟️ المركب</label>
                        <input class="form-control bg-light" value="{{ $complex->nom }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">🤸 النشاط</label>
                        <input class="form-control bg-light" value="{{ $activity->title }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📋 الجداول المتاحة --}}
        <div class="card shadow-sm rounded-4 mb-5 overflow-hidden">
            <div class="card-body p-4">
                <h5 class="fw-bold text-secondary mb-4">📋 اختر الفوج المناسب لك</h5>

                @if(!$hasSchedules)
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x fs-1"></i>
                        <p class="mt-3">لا توجد أفواج متاحة حالياً لهذا النشاط</p>
                    </div>
                @else
                    <div class="row g-3 g-md-4">
                        @foreach($schedules as $schedule)
                            @php
                                // تحويل time_slots إلى array إذا كانت JSON string
                                $slots = is_string($schedule->time_slots)
                                    ? json_decode($schedule->time_slots, true)
                                    : $schedule->time_slots;

                                $slots = is_array($slots) ? $slots : [];
                            @endphp

                            <div class="col-md-6 col-lg-4">
                                <div class="schedule-card border rounded-4 p-4 h-100 d-flex flex-column position-relative transition-all">
                                    <h6 class="fw-bold text-primary mb-3">{{ $schedule->groupe }}</h6>

                                    <div class="small text-muted mb-2">
                                        🎯 الفئة: <strong>{{ $schedule->ageCategory?->name ?? 'كل الفئات' }}</strong>
                                    </div>

                                    <div class="small text-muted mb-2">
                                        📆 النوع:
                                        <span class="badge bg-info text-dark ms-1">
                                            {{ $seasonLabels[$schedule->type_season] ?? '—' }}
                                        </span>
                                    </div>

                                    <div class="small text-muted mb-2">
                                        ⚥ الجنس:
                                        <strong>
                                            {{ $schedule->sex === 'H' ? 'ذكور' : ($schedule->sex === 'F' ? 'إناث' : 'مختلط') }}
                                        </strong>
                                    </div>

                                    <div class="small text-muted mb-2">
                                        📅 <strong>{{ $schedule->sessions_count }}</strong> حصة أسبوعياً
                                    </div>

                                    <div class="small text-muted mb-3 flex-grow-1">
                                        <strong>⏰ المواعيد:</strong>
                                        <ul class="list-unstyled mt-2 mb-0">
                                            @forelse($slots as $slot)
                                                <li class="mb-1">
                                                    🕒 <strong>{{ $daysMap[$slot['day_number']] ?? '—' }}</strong>:
                                                    {{ $slot['start'] ?? '??' }} → {{ $slot['end'] ?? '??' }}
                                                </li>
                                            @empty
                                                <li class="text-muted fst-italic">لا توجد مواعيد محددة</li>
                                            @endforelse
                                        </ul>
                                    </div>

                                    <div class="mt-auto">
                                        <div class="small text-muted mb-2">
                                           
@if(!is_null($schedule->available_places) && $schedule->available_places > 0)
    <span class="text-success ms-2">
        ✅ متاح 
    </span>
@else
   <span class="text-danger ms-2">
        ❌ هذا الفوج ممتلئ
    </span>
@endif


                                        </div>

                                        <div class="price-box mb-3 text-center">
                                            💰 {{ number_format($schedule->price) }} دج
                                        </div>

                                        <button type="button"
                                                class="btn btn-primary w-100 rounded-pill shadow-sm"
                                                onclick="openSeasonPopup(this)"
                                                data-schedule-id="{{ $schedule->id }}"
                                                data-type-season="{{ $schedule->type_season }}">
                                            ✔ اختيار هذا الجدول
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </form>

    {{-- Modal اختيار الموسم --}}
    <div class="modal fade" id="seasonModal" tabindex="-1" aria-labelledby="seasonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="seasonModalLabel">📅 اختر الموسم أو الاشتراك</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body pt-0">
                    <select class="form-select form-select-lg" id="modal_season_select">
                        <option value="" disabled selected>— اختر الموسم المناسب —</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}"
                                    data-type-season="{{ $season->type_season }}">
                                {{ $season->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        إلغاء
                    </button>
                    <button type="button" class="btn btn-success rounded-pill px-5 shadow-sm" onclick="confirmSeason()">
                        ✔ تأكيد الاختيار
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    .schedule-card {
        transition: all 0.3s ease;
        background: linear-gradient(180deg, #ffffff, #f9fafb);
    }
    .schedule-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.12) !important;
    }
    .schedule-card.border-primary {
        border: 3px solid #0d6efd !important;
        background: #f0f7ff;
    }
    .price-box {
        padding: 10px 20px;
        border: 2px solid #16a34a;
        background: #ecfdf5;
        color: #16a34a;
        border-radius: 16px;
        font-weight: 800;
        font-size: 1.1rem;
    }
    @media (max-width: 576px) {
        .price-box {
            font-size: 1rem;
            padding: 8px 16px;
        }
        .schedule-card {
            padding: 1rem !important;
        }
    }
</style>
@endpush

@push('js')
<script>
    let seasonModal;
    let selectedScheduleId = null;

    document.addEventListener('DOMContentLoaded', function () {
        seasonModal = new bootstrap.Modal(document.getElementById('seasonModal'));
    });

    function openSeasonPopup(btn) {
        selectedScheduleId = btn.dataset.scheduleId;
        const scheduleType = btn.dataset.typeSeason;

        const select = document.getElementById('modal_season_select');
        select.value = "";

        // إخفاء/إظهار الخيارات حسب نوع الاشتراك
        Array.from(select.options).forEach(option => {
            if (!option.value) return;
            const seasonType = option.dataset.typeSeason;
            option.style.display = (seasonType === scheduleType) ? 'block' : 'none';
            option.disabled = (seasonType !== scheduleType);
        });

        seasonModal.show();
    }

    function confirmSeason() {
        const seasonId = document.getElementById('modal_season_select').value;

        if (!seasonId) {
            alert('⚠️ يرجى اختيار الموسم أولاً');
            return;
        }

        // تعبئة الحقول المخفية
        document.getElementById('schedule_id').value = selectedScheduleId;
        document.getElementById('season_id').value = seasonId;

        // تمييز الكارد المختار بصرياً
        document.querySelectorAll('.schedule-card').forEach(card => {
            card.classList.remove('border-primary');
        });

        const selectedBtn = document.querySelector(`[data-schedule-id="${selectedScheduleId}"]`);
        if (selectedBtn) {
            selectedBtn.closest('.schedule-card').classList.add('border-primary');
        }

        seasonModal.hide();

        // إرسال النموذج تلقائياً
        document.getElementById('reserveForm').submit();
    }
</script>
@endpush