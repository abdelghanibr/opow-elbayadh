<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Complex;
use App\Models\Reservation;
use App\Models\Activity;
use App\Models\Season;

use App\Models\Person;
use App\Models\Dossier;
use App\Models\Club;

use App\Models\ComplexActivity;
use App\Models\Schedule;

use App\Models\PricingPlan;
use App\Models\PoolClosure;
use App\Models\ReservationCredit;
use Carbon\CarbonPeriod;

class ReservationController extends Controller
{
    private const DAY_LABELS = [
        'الأحد',
        'الاثنين',
        'الثلاثاء',
        'الأربعاء',
        'الخميس',
        'الجمعة',
        'السبت',
    ];

/*public function togglePayment(\App\Models\Reservation $reservation)
{
    $reservation->payment_status =
        $reservation->payment_status === 'paid' ? 'unpaid' : 'paid';  //confirmee
         $reservation->statut === 'confirmee' ? 'en_attente' : 'confirmee';
      $reservation->updated_by = auth()->id();
    $reservation->save();



    return response()->json([
        'status' => 'ok',
        'payment_status' => $reservation->payment_status
    ]);
}*/

public function togglePayment(Reservation $reservation)
{
    // Force toggle
    if ($reservation->payment_status == 'paid') {
        $reservation->payment_status = 'pending';
        $reservation->statut = 'en_attente';
    } else {
        $reservation->payment_status = 'paid';
        $reservation->statut = 'confirmee';
    }

    $reservation->updated_by = auth()->id();
    $reservation->save();

    return response()->json([
        'status' => 'ok',
        'payment_status' => $reservation->payment_status,
        'statut' => $reservation->statut,
        'updated_by' => auth()->user()->name,
        'updated_at' => $reservation->updated_at->format('Y-m-d H:i')
    ]);
}

/*public function togglePayment(Reservation $reservation)
{
    // Toggle payment
    $reservation->payment_status =
        $reservation->payment_status === 'paid' ? 'unpaid' : 'paid';
dd($reservation);
    // Sync statut with payment
    $reservation->statut =
        $reservation->payment_status === 'paid'
            ? 'confirmee'
            : 'en_attente';

    // Audit
    $reservation->updated_by = auth()->id();
    $reservation->updated_at = now();

    $reservation->save();

    return response()->json([
        'status' => 'ok',
        'payment_status' => $reservation->payment_status,
        'statut' => $reservation->statut,
        'updated_by' => auth()->user()->name,
        'updated_at' => $reservation->updated_at->format('Y-m-d H:i')
    ]);
}*/



public function index()
{
    $user = auth()->user();

    $query = Reservation::with([
        'complexActivity.complex',
        'complexActivity.activity',
        'season'
    ]);

$today = Carbon::today();
$limitDate = Carbon::today()->addDays(15);

 $schedules  = Schedule::orderBy('groupe')->get();
if ($user->type === 'person') {

$seasons = Season::where(function ($query) use ($today, $limitDate) {

    // monthly: جاري فقط
    $query->where(function ($q) use ($today) {
        $q->where('type_season', 'monthly')
          ->where('date_debut', '<=', $today)
          ->where('date_fin', '>=', $today);
    })

  


    // القادمة خلال 45 يوم (أي نوع)
    ->orWhere(function ($q) use ($today, $limitDate) {
        $q->where('date_debut', '>', $today)
          ->where('date_debut', '<=', $limitDate);
    });

})
->orderBy('date_debut')
->get();


}elseif (in_array($user->type, ['club', 'company'])) {

    // 🏢 نادي / مؤسسة → مواسم موسمية
    $seasons = Season::where(function ($query) use ($today, $limitDate) {

        // 🟢 season جاري
        $query->where(function ($q) use ($today) {
            $q->where('type_season', 'season')
              ->where('date_debut', '<=', $today)
              ->where('date_fin', '>=', $today);
        })

        // 🔵 season قادم خلال 45 يوم
        ->orWhere(function ($q) use ($today, $limitDate) {
            $q->where('type_season', 'season')
              ->where('date_debut', '>', $today)
              ->where('date_debut', '<=', $limitDate);
        });

    })
    ->orderBy('date_debut')
    ->get();

} else {

    // احتياط
    $seasons = collect();
}


   
    // 🧑‍💼 مستخدم عادي → حجوزاته فقط
if ($user->type != 'admin') {

    $query->where('user_id', $user->id);

    $reservations = $query->get();

    $activities = Activity::orderBy('title')->get();

    // الرصيد التعويضي المتاح للمستخدم
    $availableCredit = ReservationCredit::where('user_id', $user->id)
        ->where('status', 'pending')
        ->sum('credited_amount');

    return view('reservation.my_reservations', compact(
        'reservations',
        'activities',
        'seasons',
        'schedules',
        'availableCredit'
    ));
}

    // 🛡️ Admin مرتبط بمجمع → إظهار حجوزات مجمع واحد فقط
    if (!empty($user->complex_id) && $user->complex_id != 0) {

        $query->whereHas('complexActivity', function ($q) use ($user) {
            $q->where('complex_id', $user->complex_id);
        });

        $reservations = $query->get();

 $schedules = Schedule::whereHas('complexActivity', function ($q) use ($user) {
        $q->where('complex_id', $user->complex_id);
    })
    ->orderBy('groupe')
    ->get();

        // ⚙ تحميل Complex واحد فقط
        $complexes  = Complex::where('id', $user->complex_id)->get();

        $activities = Activity::orderBy('title')->get();
       

        return view('admin.reservations.index', compact(
            'reservations',
            'activities',
            'seasons',
            'complexes','schedules'
        ));
    }

    // 🟢 مدير عام → عرض كل المجمعات
    $reservations = $query->get();

    $complexes  = Complex::orderBy('nom')->get();
    $activities = Activity::orderBy('title')->get();
   




    return view('admin.reservations.index', compact(
        'reservations',
        'activities',
        'seasons',
        'complexes' ,'schedules'
    ));
}




     public function create()
    {
        return view('reservations.create');
    }
public function print(Reservation $reservation)
{
    $reservation->load([
        'user',
        'complexActivity.activity'
    ]);

    return view('reservation.print', compact('reservation'));
}


    /**
     * تجديد حجز (نفس النشاط و الخطة)
     */
    public function renew($id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('reservations.renew', compact('reservation'));
    }



    // 1) اختيار نوع المركب
    public function selectType()
    {
        $types = Complex::select('type')->distinct()->get()->pluck('type');
        return view('reservation.select_type', compact('types'));
    }

    // 2) قائمة المركبات حسب النوع
    public function listByType($type)
    {
        $complexes = Complex::where('type', $type)->get();
        return view('reservation.list_complex', compact('complexes', 'type'));
    }
public function availability($complexActivityId)
{
    $capacity = ComplexActivity::findOrFail($complexActivityId)->capacite ? : 1;

    // جلب كل المواعيد المحددة لهذا المركب (schedule)
    $schedules = Schedule::where('complex_activity_id', $complexActivityId)->get();

    $calendarData = [];

    // أنشئ الأسبوع الحالي من اليوم
    $startOfWeek = now()->startOfWeek(); // الأحد
    $endOfWeek = now()->endOfWeek(); // السبت

    for ($day = $startOfWeek; $day <= $endOfWeek; $day->addDay()) {

        $date = $day->format('Y-m-d');

        foreach ($schedules as $s) {

            $reserved = Reservation::where('schedule_id', $s->id)
                ->where('start_date', $date)
                ->sum('qty_places');

            $percent = ($reserved / $capacity) * 100;

            if ($percent >= 100) {
                $color = "#d32f2f";
                $label = "ممتلئ";
            } elseif ($percent >= 50) {
                $color = "#ffa000";
                $label = "متاح بعدد قليل";
            } else {
                $color = "#4caf50";
                $label = "متاح";
            }

            // بناء الحدث
            $calendarData[] = [
                'date' => $date,
                'start' => $s->heure_debut,
                'end' => $s->heure_fin,
                'color' => $reserved > 0 ? $color : '#4caf50', 
                'label' => $reserved > 0 ? $label : 'متاح',
            ];
        }
    }

    return view('reservations.availability', compact('calendarData'));
}

public function form($complexId)
{
    $user = Auth::user();

    /* =============================
       1️⃣ التحقق من النشاط المختار
    ============================== */
    $activityId = session('activity_id');

    if (!$activityId) {
        return redirect()
            ->route('reservation.select_type')
            ->with('error', '⚠ يرجى اختيار النشاط قبل المتابعة.');
    }

    /* =============================
       2️⃣ جلب البيانات الأساسية
    ============================== */
    $complex  = Complex::findOrFail($complexId);
    $activity = Activity::findOrFail($activityId);

    $complexActivity = ComplexActivity::where([
        'complex_id'  => $complexId,
        'activity_id' => $activityId,
    ])->firstOrFail();

    /* =============================
       3️⃣ معلومات الشخص (إن وُجد)
    ============================== */
    $person = null;
    $ageCategoryId = null;
    $genderCode = null;

    if ($user->type === 'person') {
        $person = Person::with('ageCategory')
            ->where('user_id', $user->id)
            ->first();

        $ageCategoryId = $person?->age_category_id;
        $genderCode    = $this->normalizeGender($person?->gender);
    }

    /* =============================
       4️⃣ المواسم
    ============================== */
//    $seasons          = Season::all();



$today = Carbon::today();
$limitDate = Carbon::today()->addDays(15);

$seasons = Season::where(function ($query) use ($today, $limitDate) {

    // monthly: جاري فقط
    $query->where(function ($q) use ($today) {
        $q->where('type_season', 'monthly')
          ->where('date_debut', '<=', $today)
          ->where('date_fin', '>=', $today);
    })

    // season: جاري فقط
    ->orWhere(function ($q) use ($today) {
        $q->where('type_season', 'season')
          ->where('date_debut', '<=', $today)
          ->where('date_fin', '>=', $today);
    })

    // القادمة خلال 45 يوم (أي نوع)
    ->orWhere(function ($q) use ($today, $limitDate) {
        $q->where('date_debut', '>', $today)
          ->where('date_debut', '<=', $limitDate);
    });

})
->orderBy('date_debut')
->get();

//dd($seasons);
    $selectedSeasonId = request('season_id');

    /* =============================
       5️⃣ الجداول الزمنية (Schedules)
    ============================== */
 $scheduleQuery = Schedule::with('ageCategory')
    ->where('complex_activity_id', $complexActivity->id)
    ->where('active', 1);

    /** --------- الأشخاص --------- */
    if ($user->type === 'person') {

        // جداول عامة فقط
        $scheduleQuery->whereNull('user_id');

        // الفئة العمرية
        if ($ageCategoryId) {
            $scheduleQuery->where(function ($q) use ($ageCategoryId) {
                $q->whereNull('age_category_id')
                  ->orWhere('age_category_id', $ageCategoryId)
                  ->orWhere('age_category_id', 5); // joker = للجميع
            });
        } else {
            $scheduleQuery->whereNull('age_category_id');
        }

        // الجنس
        if ($genderCode) {
            $scheduleQuery->where(function ($q) use ($genderCode) {
                $q->whereNull('sex')
                  ->orWhere('sex', 'X')
                  ->orWhere('sex', $genderCode);
            });
        } else {
            $scheduleQuery->where(function ($q) {
                $q->whereNull('sex')->orWhere('sex', 'X');
            });
        }
    }

    /** --------- أندية / شركات --------- */
    if (in_array($user->type, ['club', 'company'])) {
        $scheduleQuery->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere(function ($sq) {
                  $sq->where('type_prix', 'fix')
                     ->whereNull('user_id')
                     ->whereNull('age_category_id')
                     ->where(function ($s) {
                         $s->whereNull('sex')->orWhere('sex', 'X');
                     });
              });
        });
    }

    $schedules = $scheduleQuery->get();

    /* =============================
       6️⃣ حساب الأماكن المتاحة
    ============================== */
    if ($schedules->isNotEmpty()) {
      
      
      
    /*   $reservedCounts = Reservation::whereIn(
        'schedule_id',
        $schedules->pluck('id')
    )
    ->selectRaw('schedule_id, SUM(qty_places) as reserved')
    ->groupBy('schedule_id')
    ->pluck('reserved', 'schedule_id');*/
    
    // seulement les reservation dans le present date fin n est pas ecouls 
   $reservedCounts = Reservation::whereIn(
        'schedule_id',
        $schedules->pluck('id')
    )
    ->whereDate('end_date', '>=', now()->toDateString()) // غير منتهية
    ->where('payment_status', 'paid')                    // 👈 مدفوعة فقط
    ->selectRaw('schedule_id, SUM(qty_places) as reserved')
    ->groupBy('schedule_id')
    ->pluck('reserved', 'schedule_id');


    
    
    
    
    
    
    
    $schedules = $schedules->map(function ($schedule) use ($reservedCounts) {

    $reserved = (int) ($reservedCounts[$schedule->id] ?? 0);

    $schedule->reserved_places = $reserved;

    // إذا كان الجدول له سعة قصوى (nbr)
    $schedule->available_places = $schedule->nbr
        ? max(0, $schedule->nbr - $reserved)
        : null;

    return $schedule;
});

    }
//dd( $reserved);
    /* =============================
       7️⃣ التحقق من الدوسيي
    ============================== */
    if (in_array($user->type, ['club', 'company'])) {

        $dossier = Club::where('user_id', $user->id)->first();

        if (!$dossier) {
            return view('errors.error-dossier', [
                'message' => 'لا يمكنك الحجز بدون ملف مُسجل.'
            ]);
        }

        if ($dossier->etat !== 'approved') {
            return view('errors.error-dossier', [
                'message' => 'ملفك قيد المراجعة، يرجى انتظار المصادقة.'
            ]);
        }

    } else {
        if ($person) {
            $dossier = Dossier::where('owner_type', 'person')
                ->where('person_id', $person->id)
                ->first();

            if (!$dossier || $dossier->etat !== 'approved') {
                return view('errors.error-dossier', [
                    'message' => '⚠️ ملفك غير مكتمل أو غير مصادق عليه.'
                ]);
            }
        }
    }

    /* =============================
       8️⃣ العرض
    ============================== */
    return view('reservation.form', compact(
        'complex',
        'complexActivity',
        'activity',
        'seasons',
        'selectedSeasonId',
        'schedules'
    ));
}

public function renewStore(Request $request, Reservation $reservation)
{
    
   $request->validate([
    'season_id' => 'required|exists:seasons,id',
]);
  /*  $request->validate([
        'season_id'  => 'required|exists:seasons,id',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date'   => 'required|date|after:start_date',
    ]);*/
//dd($request);
    $user = auth()->user();

    /* =====================================================
       1️⃣ جلب الموسم الجديد
    ===================================================== */
    $season = Season::findOrFail($request->season_id);

    /* =====================================================
       2️⃣ جلب الجدول المرتبط بالحجز القديم
    ===================================================== */
    $schedule = Schedule::findOrFail($reservation->schedule_id);

    if (!$schedule->price) {
        return back()->with('error', '⚠ لا يوجد سعر محدد لهذا الجدول.');
    }

    $renewSlots = $this->decodeScheduleSlots($schedule);
    $renewSessionsPerWeek = count($renewSlots);

    /* =====================================================
       3️⃣ حساب السعر بنفس منطق store
    ===================================================== */
    $totalPrice = $this->calculatePlanPrice(
        (float) $schedule->price,
        $season->type_season,
        $season->date_debut,
        $season->date_fin,
        $renewSessionsPerWeek
    );

  $availableCredit = ReservationCredit::where('user_id', $user->id)
    ->where('status', 'pending')
    ->sum('credited_amount');

    $finalPrice = max(0, $totalPrice - $availableCredit);
    /* =====================================================
       4️⃣ حساب عدد الأماكن (نفس store)
    ===================================================== */
    if ($user->type === 'person') {
        $qtyPlaces = 1;
    } elseif (in_array($user->type, ['club', 'company'])) {
        $qtyPlaces = Person::where('user_id', $user->id)->count();

        if ($qtyPlaces <= 0) {
            return back()->with('error', '⚠ لا يوجد أفراد مسجلون ضمن هذا الحساب.');
        }
    } else {
        $qtyPlaces = 1;
    }


$alreadyExists = Reservation::where('user_id', $user->id)
    ->where('schedule_id', $reservation->schedule_id)
    ->where('season_id', $season->id)

    ->exists();

if ($alreadyExists) {
    return back()->with(
        'error',
        '⚠ لديك حجز موجود بالفعل لهذا الموسم، لا يمكن تجديده مرتين مهما كانت حالته.'
    );
}

    /* =====================================================
       5️⃣ إنشاء حجز جديد (clone نظيف)
    ===================================================== */
    $newReservation = $reservation->replicate([
        'statut',
        'payment_status',
    ]);

    $newReservation->season_id      = $season->id;
    $newReservation->start_date     = $season->date_debut;
    $newReservation->end_date       = $season->date_fin;
    $newReservation->time_slots     = $reservation->time_slots;
    $newReservation->duration_hours = $reservation->duration_hours;
    $newReservation->qty_places     = $qtyPlaces;
     $newReservation->total_price    = round($finalPrice);
    $newReservation->statut          = 'en_attente';
    $newReservation->payment_status = 'pending';

    $newReservation->save();

    return redirect()
        ->route('reservation.my-reservations')
        ->with('success', '✔ تم تجديد الحجز بنجاح بنفس تسعيرة الموسم المختار.');
}





    // 4) تنفيذ الحجز
public function store(Request $request)
{
    $request->validate([
        'complex_activity_id' => 'required|exists:complex_activity,id',
        'season_id'           => 'required|exists:seasons,id',
        'schedule_id'         => 'required|exists:schedules,id',
    ], [
        'complex_activity_id.required' => '⚠ يرجى اختيار مركب ونشاط صحيح.',
        'season_id.required'           => '⚠ يرجى اختيار الموسم الرياضي.',
        'schedule_id.required'         => '⚠ يرجى اختيار جدول زمني للانضمام إليه.',
    ]);

    $user = Auth::user();

    /* =====================================================
       1️⃣ جلب المعطيات الأساسية
    ===================================================== */
    $complexActivity = ComplexActivity::findOrFail($request->complex_activity_id);
    $schedule        = Schedule::findOrFail($request->schedule_id);
    $season          = Season::findOrFail($request->season_id);

    /* =====================================================
       2️⃣ منع الحجز المكرر لنفس المستخدم ونفس الجدول ونفس الموسم
    ===================================================== */
    $alreadyExists = Reservation::where('user_id', $user->id)
        ->where('schedule_id', $schedule->id)
        ->where('season_id', $season->id)
        ->exists();

    if ($alreadyExists) {
        return back()->with(
            'error',
            '⚠ لديك حجز موجود بالفعل لهذا الموسم، لا يمكن تجديده مرتين مهما كانت حالته.'
        );
    }

    /* =====================================================
       3️⃣ تحقق من انتماء الجدول للنشاط
    ===================================================== */
    if ((int) $schedule->complex_activity_id !== (int) $complexActivity->id) {
        return back()->with('error', '⚠ الجدول المختار لا ينتمي إلى هذا النشاط.');
    }

    /* =====================================================
       4️⃣ التحقق من ملف المستخدم
    ===================================================== */
    $person = $user->type === 'person'
        ? Person::where('user_id', $user->id)->first()
        : null;

    $ageCategoryId = $person?->age_category_id;
    $genderCode    = $this->normalizeGender($person?->gender);

    if (!$this->scheduleMatchesUserProfile($schedule, $ageCategoryId, $genderCode)) {
        return back()->with('error', '⚠ هذا الجدول غير متاح لبياناتك الشخصية.');
    }

    /* =====================================================
       5️⃣ استخراج الحصص الأسبوعية
    ===================================================== */
    $slots = $this->decodeScheduleSlots($schedule);
    $sessionsPerWeek = count($slots);

    /* =====================================================
       6️⃣ منع التعارض الزمني
    ===================================================== */
    $conflict = $this->checkScheduleConflict($user->id, $schedule, $season);

    if ($conflict) {
        return back()->with('error', '⚠ يوجد تعارض في المواعيد مع حجز آخر لديك: ' . $conflict);
    }

    /* =====================================================
       7️⃣ تحديد عدد الأماكن المطلوبة حسب نوع المستخدم
    ===================================================== */
    if ($user->type === 'person') {

        $qtyPlaces = 1;

    } elseif (in_array($user->type, ['club', 'company'])) {

        $qtyPlaces = Person::where('user_id', $user->id)->count();

        if ($qtyPlaces <= 0) {
            return back()->with('error', '⚠ لا يوجد أفراد مسجلون ضمن هذا الحساب.');
        }

    } else {
        $qtyPlaces = 1;
    }

    /* =====================================================
       8️⃣ التحقق من السعة
       نحسب فقط الحجوزات المدفوعة خلال آخر شهر لنفس الجدول
    ===================================================== */
    if ($schedule->nbr) {

       $dateFrom = now()->copy()->startOfMonth();
       $dateTo   = now()->copy()->addMonth()->endOfMonth();

        $reservedPlaces = Reservation::where('schedule_id', $schedule->id)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('qty_places');

        $availablePlaces = (int) $schedule->nbr - (int) $reservedPlaces;

        if ($availablePlaces <= 0) {
            return back()->with('error', '⚠ هذا الجدول ممتلئ حالياً.');
        }

        if ($qtyPlaces > $availablePlaces) {
            return back()->with(
                'error',
                '⚠ عدد الأماكن المتبقية في هذا الجدول هو ' . $availablePlaces . ' فقط، وعدد الأماكن المطلوبة هو ' . $qtyPlaces . '.'
            );
        }
    }

    /* =====================================================
       9️⃣ حساب السعر
    ===================================================== */
    if (!$schedule->price) {
        return back()->with('error', '⚠ لا يوجد سعر محدد لهذا الجدول.');
    }

    $totalPrice = $this->calculatePlanPrice(
        (float) $schedule->price,
        $season->type_season,
        $season->date_debut,
        $season->date_fin,
        $sessionsPerWeek
    );

    /* =====================================================
       🔟 احتساب الرصيد التعويضي
    ===================================================== */
    $availableCredit = ReservationCredit::where('user_id', $user->id)
        ->where('status', 'pending')
        ->sum('credited_amount');

    $finalPrice = max(0, $totalPrice - $availableCredit);

    /* =====================================================
       1️⃣1️⃣ إنشاء الحجز
    ===================================================== */
    $newReservation = Reservation::create([
        'user_id'             => $user->id,
        'complex_activity_id' => $complexActivity->id,
        'season_id'           => $season->id,
        'schedule_id'         => $schedule->id,
        'pricing_plan_id'     => null,
        'start_date'          => $season->date_debut,
        'end_date'            => $season->date_fin,
        'time_slots'          => $slots,
        'duration_hours'      => $sessionsPerWeek,
        'qty_places'          => $qtyPlaces,
        'total_price'         => $finalPrice,
        'statut'              => 'en_attente',
        'payment_status'      => 'pending',
    ]);

    /*
    if ($availableCredit > 0) {
        ReservationCredit::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'used',
                'used_in_reservation_id' => $newReservation->id,
            ]);
    }
    */

    return redirect()
        ->route('reservation.my-reservations')
        ->with('success', '✔ تم تسجيل الحجز بنجاح وسيتم مراجعته من الإدارة.');
}

 public function pay(Reservation $reservation)
    {
        // 🔐 تأكد أن الحجز يخص المستخدم الحالي
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالدفع لهذا الحجز');
        }

        // ✅ إذا كان مدفوعًا بالفعل
        if ($reservation->payment_status === 'paid') {
            return back()->with('info', 'ℹ️ هذا الحجز مدفوع بالفعل');
        }

        // 🟡 pending أو 🔴 failed → نسمح بالدفع
        return view('payments.pay', [
            'reservation' => $reservation
        ]);
    }

    // 8) حجوزات المستخدم
    public function myReservations()
    {
        $reservations = Reservation::where('user_id', auth()->id())->get();// جلب حجوزات المستخدم الحالي
        return view('reservation.my_reservations', compact('reservations'));// عرض صفحة الحجوزات مع تمرير الحجوزات
    }




    private function eligiblePricingPlans($activityId, $user, ?int $ageCategoryId = null, ?string $gender = null)// جلب خطط التسعير المؤهلة بناءً على معايير المستخدم
    {
       // dd($activityId, $user, $ageCategoryId, $gender);
        $typeClient = match ($user->type) {// تحديد نوع العميل بناءً على نوع المستخدم
            'club' => 'club',// إذا كان المستخدم نادي
            'company' => 'company',// إذا كان المستخدم شركة
            default => 'person',// إذا كان المستخدم فرد
        };

        return PricingPlan::where('activity_id', $activityId) // جلب خطط التسعير للنشاط المحدد
            ->where('active', 1)// التحقق من أن خطة التسعير نشطة
            ->where('type_client', $typeClient)// التحقق من نوع العميل
            ->whereDate('valid_from', '<=', now())// التحقق من تاريخ صلاحية خطة التسعير
            ->where(function ($query) {// شرط للتحقق من تاريخ انتهاء صلاحية خطة التسعير
                $query->whereNull('valid_to')// إذا لم يكن هناك تاريخ انتهاء
                      ->orWhereDate('valid_to', '>=', now()) ;// التحقق من صلاحية خطة ا
                     // Cas joker : si la plan a age_category_id = 5 → elle est valable pour TOUS les âges
                
            }) 
            ->when($ageCategoryId, function ($query) use ($ageCategoryId) {// التحقق من فئة العمر
                $query->where(function ($q) use ($ageCategoryId) {// شرط للتحقق من فئة العمر
                    $q->whereNull('age_category_id') // إذا لم تكن هناك فئة عمر محددة  
                      ->orWhere('age_category_id', $ageCategoryId)
                      ->orWhere('age_category_id', 5);// التحقق من فئة العمر
                });// التحقق من فئة العمر
            })
            ->when($gender, function ($query) use ($gender) {// التحقق من الجنس
                $query->where(function ($q) use ($gender) {// شرط للتحقق من الجنس
                    $q->whereNull('sexe')// إذا لم يكن هناك جنس محدد
                      ->orWhere('sexe', $gender)// التحقق من الجن
                      // Cas joker : si la plan a sexe = 'X' → elle est valable pour TOUS les genres
                     ->orWhere('sexe', 'X');
                });// التحقق من الجنس
            })
            ->get();// جلب خطط التسعير المؤهلة بناءً على المعايير المحددة
  //  dd()
    
        }

    private function normalizeGender(?string $value): ?string// تطبيع قيمة الجنس
    {
        if (!$value) {// إذا لم تكن هناك قيمة
            return null;// إرجاع null
        }

        $value = strtolower(trim($value));// تحويل القيمة إلى حروف صغيرة وإزالة الفراغات

        return match (true) {// استخدام تعبير match للتحقق من القيم المختلفة
            in_array($value, ['h', 'homme', 'male', 'm']) => 'H',// إذا كانت القيمة تشير إلى ذكر
            in_array($value, ['f', 'femme', 'female']) => 'F',// إذا كانت القيمة تشير إلى أنثى
            default => null,// إذا لم تتطابق مع أي من القيم المعروفة، إرجاع null
        };
    }

private function scheduleMatchesUserProfile(Schedule $schedule, ?int $ageCategoryId, ?string $gender): bool
{
    // تحقق من فئة العمر
    if ($ageCategoryId !== null && $schedule->age_category_id !== null) {
        // إذا كان الجدول يحدد فئة عمر = 5 → جoker: يقبل جميع الأعمار
        if ((int) $schedule->age_category_id === 5) {
            // يطابق أي عمر → نستمر
        }
        // خلاف ذلك، يجب أن تتطابق الفئة بالضبط
        elseif ((int) $schedule->age_category_id !== (int) $ageCategoryId) {
            return false;
        }
    }
    // إذا كان أحد الطرفين null → مقبول (لا قيد)

    // تحقق من الجنس
    if ($gender !== null && $schedule->sex !== null) {
        $scheduleSex = strtoupper(trim($schedule->sex));

        // إذا كان الجدول يحدد 'X' → جoker: يقبل جميع الأجناس (M أو F)
        if ($scheduleSex === 'X') {
            // يطابق أي جنس → نستمر
        }
        // خلاف ذلك، يجب أن يتطابق الجنس بالضبط
        elseif ($scheduleSex !== strtoupper($gender)) {
            return false;
        }
    }
    // إذا كان أحد الطرفين null → مقبول (لا قيد)

    return true; // كل الشروط تم التحقق منها بنجاح
}

    private function decodeScheduleSlots(Schedule $schedule): array// فك تشفير الفتحات الزمنية للجدول
    {
        $slots = $schedule->time_slots;// الحصول على الفتحات الزمنية من الجدول

        if (is_string($slots)) {// إذا كانت الفتحات الزمنية عبارة عن سلسلة نصية
            $decoded = json_decode($slots, true);// فك تشفير السلسلة النصية إلى مصفوفة
            if (json_last_error() === JSON_ERROR_NONE) {// التحقق من عدم وجود أخطاء في فك التشفير
                return $decoded ?: [];// إرجاع المصفوفة المفككة أو مصفوفة فارغة إذا كانت null
            }
            return [];// إرجاع مصفوفة فارغة في حالة وجود خطأ في فك التشفير
        }

        return $slots ?: [];//  إرجاع الفتحات الزمنية كما هي أو مصفوفة فارغة إذا كانت null
    }

    private function decorateScheduleForDisplay(Schedule $schedule, Collection $pricingPlans): Schedule // تزيين الجدول للعرض
    {
        $slots = $this->decodeScheduleSlots($schedule);// فك تشفير الفتحات الزمنية للجدول
        $sessionsCount = count($slots);// حساب عدد الجلسات في الأسبوع

        $schedule->sessions_count = $sessionsCount;// تعيين عدد الجلسات في الجدول
        $schedule->formatted_slots = collect($slots)->map(function ($slot) {// تنسيق الفتحات الزمنية للعرض
            $dayIndex = $slot['day_number'] ?? null;// الحصول على رقم اليوم
            $dayLabel = $dayIndex !== null ? (self::DAY_LABELS[$dayIndex] ?? 'يوم غير معروف') : 'يوم غير معروف';// تعيين تسمية اليوم

            $start = $slot['start'] ?? null;// الحصول على وقت البدء
            $end   = $slot['end'] ?? null;// الحصول على وقت الانتهاء

            return trim($dayLabel . ' | ' . ($start ?? '?') . ' → ' . ($end ?? '?'));// 
        })->toArray();// تحويل المجموعة إلى مصفوفة

        if ($schedule->type_prix === 'pricing_plan') {// إذا كانت خطة التسعير
            $matchedPlan = $this->matchPlanForSchedule($schedule, $pricingPlans, $sessionsCount);// مطابقة خطة التسعير للجدول
            $schedule->applied_plan = $matchedPlan;// تعيين خطة التسعير المطبقة
            $schedule->calculated_price = $matchedPlan?->price;// تعيين السعر المحسوب بناءً على خطة التسعير
            $schedule->pricing_note = $matchedPlan ? $this->pricingUnitLabel($matchedPlan) : null;// تعيين ملاحظة التسعير بناءً على خطة التسعير
        } else {
            $schedule->applied_plan = null;// لا توجد خطة تسعير مطبقة
            $schedule->calculated_price = $schedule->price;// تعيين السعر الثابت للجدول
            $schedule->pricing_note = 'سعر ثابت';// 
        }

        return $schedule;
    }

    private function matchPlanForSchedule(Schedule $schedule , Collection $pricingPlans, int $sessionsPerWeek)// مطابقة خطة التسعير للجدول
    {
        return $pricingPlans->first(function ($plan) use ($schedule, $sessionsPerWeek) {// البحث عن أول خطة مطابقة
            if ((int) $plan->sessions_per_week !== $sessionsPerWeek) {// التحقق من عدد الحصص في الأسبوع
                return false;// إذا لم يتطابق، إرجاع false
            }

            if ($schedule->age_category_id && $plan->age_category_id && (int) $plan->age_category_id !== (int) $schedule->age_category_id) {// التحقق من فئة العمر
                return false;// إذا لم تتطابق، إرجاع false
            }

            $scheduleSex = $schedule->sex ? strtoupper($schedule->sex) : 'X';// الحصول على جنس الجدول
            if ($plan->sexe && $scheduleSex !== 'X' && strtoupper($plan->sexe) !== $scheduleSex) {// التحقق من الجنس
                return false;// إذا لم يتطابق، إرجاع false
            }

            return true;// إذا تطابقت كل الشروط، إرجاع true
        });
    }

    private function pricingUnitLabel(?PricingPlan $plan): ?string// تسمية وحدة التسعير
    {
        if (!$plan) {// إذا لم تكن هناك خطة
            return null;
        }

        $unit = strtolower($plan->duration_unit ?? $plan->pricing_type ?? '');// وحدة التسعير

        return match ($unit) {// تحديد التسمية بناءً على وحدة التسعير
            'monthly', 'month' => 'السعر لكل شهر',// السعر لكل شهر
            'weekly', 'week' => 'السعر لكل أسبوع',// السعر لكل أسبوع
            'session' => 'السعر لكل حصة',// السعر لكل حصة
            'ticket' => 'سعر التذكرة',// سعر التذكرة
            default => 'سعر الخطة',//   
        };
    }











    private function calculateSchedulePrice(Schedule $schedule, Season $season, PricingPlan $plan, int $sessionsPerWeek): float// حساب سعر الجدول
    {
       // if ($schedule->type_prix !== 'pricing_plan') {// إذا لم تكن خطة التسعير
       //     return $this->calculateFixedSchedulePrice($schedule, $season);// حساب السعر الثابت للجدول
     //   }

        return $this->calculatePlanPrice($plan, $season, $sessionsPerWeek);// حساب سعر الخطة بناءً على الموسم وعدد الحصص في الأسبوع
    }



private function calculatePlanPrice(
    float $basePrice,
    string $type,
    string $dateDebut,
    string $dateFin,
    int $sessionsPerWeek = 1
): float {

    $today     = Carbon::today();
    $startDate = Carbon::parse($dateDebut);
    $endDate   = Carbon::parse($dateFin);

    if ($today->greaterThan($endDate)) {
        return 0;
    }

    $unit = strtolower($type);

    /* =====================================================
       MOIS / SAISON — calcul par sessions
       prix_par_session = basePrice / (sessions/semaine × semaines)
       montant = prix_par_session × sessions_restantes
    ===================================================== */
    if (in_array($unit, ['month', 'monthly', 'season'])) {

        if ($today->lessThan($startDate)) {
            return round($basePrice, 2);
        }

        $effectiveEnd = $endDate->copy();
        if (in_array($unit, ['month', 'monthly'])) {
            $endOfMonth = $today->copy()->endOfMonth();
            if ($endOfMonth->lessThan($endDate)) {
                $effectiveEnd = $endOfMonth;
            }
        }

        $totalDays     = $startDate->diffInDays($endDate) + 1;
        $totalWeeks    = (int) ceil($totalDays / 7);
        $totalSessions = $sessionsPerWeek * $totalWeeks;

        if ($totalSessions <= 0) {
            return round($basePrice, 2);
        }

        $remainingDays     = $today->diffInDays($effectiveEnd) + 1;
        $remainingWeeks    = (int) ceil($remainingDays / 7);
        $remainingSessions = $sessionsPerWeek * $remainingWeeks;

        $pricePerSession = $basePrice / $totalSessions;

        return round($pricePerSession * $remainingSessions, 2);
    }

    return round($basePrice, 2);
}





























    private function calculateFixedSchedulePrice(Schedule $schedule, Season $season): float// حساب السعر الثابت للجدول
    {
        $basePrice = (float) $schedule->price;// السعر الأساسي للجدول
        $start = Carbon::parse($season->date_debut);// تاريخ بداية الموسم
        $end = Carbon::parse($season->date_fin);// تاريخ نهاية الموسم
        $today = Carbon::today();// تاريخ اليوم

        // إذا بدأ الاشتراك في منتصف الشهر، احسب السعر التناسبي
        $proratedFirstMonth = $this->calculateProratedFirstMonth($today, $basePrice);
        
        $monthsBetween = $start->diffInMonths($end);// حساب عدد الشهور بين البداية والنهاية

        if ($end->day >= $start->day || $monthsBetween === 0) {// إذا كان يوم النهاية أكبر أو يساوي يوم البداية
            $monthsBetween += 1;// زيادة عدد الشهور بمقدار 1
        }

        $months = max(1, $monthsBetween);// التأكد من أن عدد الشهور لا يقل عن 1
        $monthsCharged = min(12, $months);// تحديد عدد الشهور التي سيتم احتسابها (بحد أقصى 12)

        // إذا كان هناك سعر تناسبي للشهر الأول، اطرح شهر كامل واضف السعر التناسبي
        if ($proratedFirstMonth < $basePrice && $monthsCharged > 0) {
            return ($basePrice * ($monthsCharged - 1)) + $proratedFirstMonth;
        }
    //   dd( $schedule->price);
        return  $schedule->price; // $basePrice * $monthsCharged;// حساب السعر النهائي بضرب السعر الأساسي في عدد الشهور المحتسبة
    }

   /* private function calculatePlanPrice(PricingPlan $plan, Season $season, int $sessionsPerWeek): float // حساب سعر الخطة
    {
        $start = Carbon::parse($season->date_debut);    // تاريخ بداية الموسم
        $end = Carbon::parse($season->date_fin);// تاريخ نهاية الموسم
        $today = Carbon::today();// تاريخ اليوم
        $unit = strtolower($plan->duration_unit ?? $plan->pricing_type ?? 'season');// وحدة التسعير
        $durationValue = max(1, (int) ($plan->duration_value ?? 1));// قيمة المدة
        $basePrice = (float) $plan->price;// السعر الأساسي للخطة

        $days = $start->diffInDays($end) + 1;// عدد الأيام في الموسم
        $weeks = max(1, (int) ceil($days / 7));// حساب الأسابيع
        $months = max(1, (int) ceil($days / 30));// حساب الشهور

        // حساب السعر التناسبي للشهر الأول إذا بدأ الاشتراك في منتصف الشهر
        $proratedFirstMonth = $this->calculateProratedFirstMonth($today, $basePrice);
        $hasProratedMonth = $proratedFirstMonth < $basePrice;

        $totalPrice = match ($unit) { // تحديد السعر بناءً على وحدة التسعير
            'month', 'monthly' => $basePrice, // ceil($months / $durationValue) * $basePrice,// السعر مضروب في عدد الشهور مقسوم على قيمة المدة
            'week', 'weekly' => ceil($weeks / $durationValue) * $basePrice,// السعر مضروب في عدد الأسابيع مقسوم على قيمة المدة
            'session' => $weeks * min(1, $plan->sessions_per_week ?? $sessionsPerWeek) * $basePrice,// السعر مضروب في عدد الأسابيع وعدد الحصص في الأسبوع
            'ticket' => $basePrice,// سعر التذكرة ثابت
            default => $basePrice,// السعر الأساسي للخطة
        };

        // إذا كان النوع شهري وبدأ الاشتراك في منتصف الشهر، استخدم السعر التناسبي
        if (in_array($unit, ['month', 'monthly']) && $hasProratedMonth) {
            return $proratedFirstMonth;
        }

        return $totalPrice;
    }*/
    
    
 // @return array{price: float|null, error: string|null}
/*private function calculatePlanPrice(PricingPlan $plan, Season $season, int $sessionsPerWeek): float
{
    $startDate = Carbon::parse($season->date_debut);  // ex: 2025-09-01
    $endDate   = Carbon::parse($season->date_fin);    // ex: 2026-06-01
    $today     = Carbon::today();                     // 2025-12-23 (date actuelle)

    $seasonType      = strtolower($season->type_season ?? '');
    $planPricingType = strtolower($plan->pricing_type ?? '');

    // Optionnel : réactiver plus tard pour bloquer les incohérences
    // if ($seasonType && $planPricingType && $seasonType !== $planPricingType) { ... }
    if ($planPricingType === 'monthly' || $planPricingType === 'month') {
        // السعر الأساسي يغطي 30 يومًا فقط
        $totalDaysInSeason = 30;
        
    } else {
        
       $totalDaysInSeason = $startDate->diffInDays($endDate) + 1;
        
    }
 //dd($totalDaysInSeason);


    $unit = strtolower($season->type_season ?? $plan->pricing_type ?? 'season');
    $durationValue = max(1, (int) ($plan->duration_value ?? 1));
    $basePrice     = (float) $plan->price;

    // عدد الأيام الكلي في الموسم (شامل يوم البداية والنهاية)
 

    // عدد الأسابيع والأشهر (للوحدات الأخرى)
    $weeksInSeason  = max(1, (int) ceil($totalDaysInSeason / 7));
    $monthsInSeason = max(1, $startDate->diffInMonths($endDate) + 1);

    $totalPrice = $basePrice;

    switch ($unit) {
        case 'month':
        case 'monthly':
            $periods = (int) ceil($monthsInSeason / $durationValue);
            $totalPrice = $periods * $basePrice;

            if ($startDate->lessThanOrEqualTo($today)) {
                $proratedFirstMonth = $this->calculateProratedFirstMonth($startDate, $basePrice);
                if ($proratedFirstMonth < $basePrice) {
                    $totalPrice = $totalPrice - $basePrice + $proratedFirstMonth;
                }
            }
            break;

        case 'week':
        case 'weekly':
            $periods = (int) ceil($weeksInSeason / $durationValue);
            $totalPrice = $periods * $basePrice;
            break;

        case 'session':
            $sessions = max(1, (int) ($plan->sessions_per_week ?? $sessionsPerWeek));
            $totalPrice = $weeksInSeason * $sessions * $basePrice;
            break;

        case 'ticket':
            $totalPrice = $basePrice;
            break;

        case 'season':
        default:
            // السعر الأساسي هو للموسم كاملاً
            $totalPrice = $basePrice;

            // إذا بدأ الموسم قبل أو اليوم → نحسب prorata على الأيام المتبقية من اليوم إلى النهاية
            if ($startDate->lessThanOrEqualTo($today)) {
                // الأيام المتبقية من اليوم (شامل) إلى نهاية الموسم
                $remainingDays = $today->diffInDays($endDate) + 1;

                // إذا كانت الأيام المتبقية أقل من إجمالي الأيام → نطبق prorata
               // if ($remainingDays < $totalDaysInSeason) {
                    $dailyPrice = $basePrice / $totalDaysInSeason;
                    $totalPrice = $dailyPrice * $remainingDays;
               // }
                // إذا كانت = أو أكثر → يدفع الكامل (لن يحدث إلا إذا كان اليوم قبل البداية، لكن الشرط يمنعه)
            }
            // إذا كان الموسم لم يبدأ بعد → يدفع الكامل (لا prorata)
            break;
    }

    return round($totalPrice, 2);
}*/



    /**
     * حساب السعر التناسبي للشهر الأول إذا بدأ الاشتراك بعد اليوم الأول من الشهر
     * Calculate prorated price if subscription starts mid-month
     */
   /* private function calculateProratedFirstMonth(Carbon $startDate, float $monthlyPrice): float
    {
        $dayOfMonth = $startDate->day;// اليوم من الشهر (1-31)
        
        // إذا بدأ الاشتراك في اليوم الأول، لا حاجة للتقسيم التناسبي
        if ($dayOfMonth === 1) {
            return $monthlyPrice;
        }

        $daysInMonth = $startDate->daysInMonth;// عدد الأيام في الشهر الحالي
        $remainingDays = $daysInMonth - $dayOfMonth + 1;// الأيام المتبقية في الشهر (بما فيها يوم البداية)
        
        // حساب السعر التناسبي بناءً على الأيام المتبقية
        $proratedPrice = ($monthlyPrice / $daysInMonth) * $remainingDays;
        
        return round($proratedPrice, 2);// تقريب السعر لرقمين عشريين
    }*/
private function calculateProratedFirstMonth(Carbon $startDate, float $monthlyPrice): float //mofier par gh
{
    // تاريخ اليوم (بدون ساعات للمقارنة اليومية)
    $today = Carbon::today();
  
    // إذا كانت بداية الاشتراك في المستقبل → لا prorata، سعر شهري كامل
    if ($startDate->greaterThan($today)) {
        return round($monthlyPrice, 2);
    }

    // إذا كانت بداية الاشتراك في نفس اليوم أو في الماضي
    // نحدد التاريخ الفعلي لبداية الفوترة: اليوم إذا تأخر التسجيل
    $effectiveStartDate = $startDate->lessThanOrEqualTo($today) ? $today : $startDate;
  //  dd($effectiveStartDate); 
    // الشهر المرجعي هو شهر التاريخ الفعلي لبداية الفوترة
    $daysInMonth = $effectiveStartDate->daysInMonth;
    $dayOfMonth = $effectiveStartDate->day;

    // إذا بدأ في اليوم الأول من الشهر → سعر كامل
    if ($dayOfMonth === 1) {
        return round($monthlyPrice, 2);
    }

    // الأيام المتبقية في الشهر بدءًا من اليوم الفعلي (شامل اليوم الحالي)
    $remainingDays = $daysInMonth - $dayOfMonth + 1;//jeure restat dans le mois
 //dd($remainingDays) ;
    // حساب السعر التناسبي
    $proratedPrice = ($monthlyPrice / $daysInMonth) * $remainingDays;
//dd($proratedPrice);
    return round($proratedPrice, 2);
}
    /**
     * التحقق من وجود تعارض في المواعيد مع حجوزات المستخدم الحالية
     * Check if new schedule conflicts with user's existing reservations
     * 
     * @param int $userId
     * @param Schedule $newSchedule
     * @param Season $newSeason
     * @return string|null رسالة التعارض أو null إذا لم يكن هناك تعارض
     */
   private function checkScheduleConflict(
    int $userId,
    Schedule $newSchedule,
    Season $newSeason
): ?string {

    $existingReservations = Reservation::where('user_id', $userId)
        ->whereIn('statut', ['en_attente', 'validé', 'confirmé'])
        ->with('schedule')
        ->get();

    // 🟢 Slots du nouveau schedule
    $newSlots = $this->decodeScheduleSlots($newSchedule);

    // 🟢 Dates du nouveau season (Carbon)
    $seasonStart = Carbon::parse($newSeason->date_debut);
    $seasonEnd   = Carbon::parse($newSeason->date_fin);

    foreach ($existingReservations as $reservation) {

        // 🟢 Dates de réservation existante
        $existingSeasonStart = Carbon::parse($reservation->start_date);
        $existingSeasonEnd   = Carbon::parse($reservation->end_date);

        // ❌ Aucun chevauchement de saisons
        if (
            $seasonEnd->lt($existingSeasonStart) ||
            $seasonStart->gt($existingSeasonEnd)
        ) {
            continue;
        }

        // 🟢 Slots existants
        $existingSlots = is_array($reservation->time_slots)
            ? $reservation->time_slots
            : json_decode($reservation->time_slots, true) ?? [];

        foreach ($newSlots as $newSlot) {
            foreach ($existingSlots as $existingSlot) {

                $newDay      = $newSlot['day_number'] ?? null;
                $existingDay = $existingSlot['day_number'] ?? null;

                if ($newDay === null || $existingDay === null) {
                    continue;
                }

                if ($newDay !== $existingDay) {
                    continue;
                }

                // 🕒 Heures (strings)
                $newStartTime = $newSlot['start'] ?? $newSlot['start_time'] ?? null;
                $newEndTime   = $newSlot['end']   ?? $newSlot['end_time']   ?? null;

                $existStartTime = $existingSlot['start'] ?? $existingSlot['start_time'] ?? null;
                $existEndTime   = $existingSlot['end']   ?? $existingSlot['end_time']   ?? null;

                if (
                    !$newStartTime || !$newEndTime ||
                    !$existStartTime || !$existEndTime
                ) {
                    continue;
                }

                // ⛔ Conflit horaire
                if ($this->timesOverlap(
                    $newStartTime,
                    $newEndTime,
                    $existStartTime,
                    $existEndTime
                )) {

                    $dayName = $this->getDayNameInArabic($newDay);
                    $group   = optional($reservation->schedule)->groupe ?? '—';

                    return "⚠️ تعارض يوم {$dayName} من {$newStartTime} إلى {$newEndTime} مع مجموعة {$group}";
                }
            }
        }
    }

    return null;
}


    /**
     * التحقق من تداخل فترتين زمنيتين
     * Check if two time ranges overlap
     */
    private function timesOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        $s1 = strtotime($start1);
        $e1 = strtotime($end1);
        $s2 = strtotime($start2);
        $e2 = strtotime($end2);

        // التداخل يحدث إذا:
        // بداية الفترة الأولى قبل نهاية الفترة الثانية
        // ونهاية الفترة الأولى بعد بداية الفترة الثانية
        return ($s1 < $e2) && ($e1 > $s2);
    }

    /**
     * الحصول على اسم اليوم بالعربية من رقم اليوم
     * Get Arabic day name from day number (0 = Sunday)
     */
    private function getDayNameInArabic(int $dayNumber): string
    {
        return match($dayNumber) {
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
            default => 'غير معروف'
        };
    }

    public function destroy(Reservation $reservation)
{
    $reservation->delete();

    return redirect()->back()->with('success', 'تم حذف الحجز بنجاح');
}

}