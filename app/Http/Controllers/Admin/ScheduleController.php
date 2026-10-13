<?php

namespace App\Http\Controllers\Admin;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\AgeCategory;
use App\Models\Complex;
use App\Models\Activity;
use App\Models\ComplexActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * قائمة الجداول الزمنية
     */
/*    public function index()
    {
        $schedules = Schedule::with([
            'complexActivity.complex',
            'complexActivity.activity',
            'ageCategory',
            'user'
        ])->get();

        $complexes = Complex::all();
        $activities = Activity::all();

        return view('admin.schedules.index', compact('schedules', 'complexes', 'activities'));
    }*/

public function index()
    {
        $admin = Auth::user();
        
        $today = Carbon::today();
        
$scheduleSeasonCounts = DB::table('reservations')
    ->join('seasons', 'reservations.season_id', '=', 'seasons.id')
    ->select(
        'reservations.schedule_id',
        'reservations.season_id',
        'seasons.name as season_name',
        DB::raw('COUNT(*) as total')
    )
    ->groupBy(
        'reservations.schedule_id',
        'reservations.season_id',
        'seasons.name'
    )
    ->get()
    ->groupBy('schedule_id');



        // بناء query أساسية + eager loading
        $query = Schedule::with([
            'complexActivity.complex',
            'complexActivity.activity',
            'ageCategory',
            'user'
        ]);

        // إذا كان admin مرتبط بمجمّع -> عرض جدول هذا المجمّع فقط
        if (!is_null($admin->complex_id) && $admin->complex_id != 0) {

            $query->whereHas('complexActivity', function($q) use ($admin) {
                $q->where('complex_id', $admin->complex_id);
            });

            // تحميل المؤسسات والأنشطة الخاصة بهذا المجمّع فقط
            $complexes = Complex::where('id', $admin->complex_id)->get();

            $activities = Activity::whereHas('complexActivities', function($q) use ($admin){
                $q->where('complex_id', $admin->complex_id);
            })->get();
            
 
        } 
        else {
            // إذا لا يوجد complex_id -> يمكنه رؤية الجميع
            $complexes = Complex::all();
            $activities = Activity::all();
        }


        // جلب النتائج
        $schedules = $query->orderByDesc('id')->get();

        return view('admin.schedules.index', compact('schedules', 'complexes', 'activities', 'scheduleSeasonCounts'));
    }
    /**
     * صفحة إنشاء جدول جديد
     */
  /*  public function create()
    {
        $ageCategories = AgeCategory::all();
        $complexes = Complex::all();
        $activities = Activity::all();
        $users = User::whereIn('type', ['club', 'company'])->get(); // user_id اختياري

        return view('admin.schedules.create', compact(
            'ageCategories',
            'complexes',
            'activities',
            'users'
        ));
    }*/
public function create()
{
    return $this->scheduleForm();
}


    /**
     * 📋 تكرار فوج داخل نفس المركب
     * تُنسخ كل البيانات ما عدا الاسم والمدرب، وتُترك الأوقات للتعديل
     * (أو تُنسخ إن كان المركب يمنع التداخل).
     */
    public function duplicate($id)
    {
        $admin = Auth::user();
        $source = Schedule::with('complexActivity')->findOrFail($id);

        $sourceComplexId = $source->complexActivity->complex_id ?? null;

        if (!is_null($admin->complex_id) && $admin->complex_id != 0
            && (int) $sourceComplexId !== (int) $admin->complex_id) {
            abort(403, '⛔ لا يمكنك نسخ فوج من منشأة أخرى');
        }

        return $this->scheduleForm($source);
    }


    /**
     * 🧩 بيانات نموذج الإنشاء / التكرار
     */
    private function scheduleForm(?Schedule $source = null)
{
    $admin = Auth::user();

    // === فوج المصدر (وضع النسخ) ===
    if ($source) {
        $source->loadMissing('complexActivity.complex');
    }

    // === فئات الأعمار ===
    $ageCategories = AgeCategory::all();

    // === المستخدمون (اختياريين) ===
    $users = User::whereIn('type', ['club', 'company'])->get();

    // ==== إذا كان مدير عام (بدون مجمع) ====
    if (is_null($admin->complex_id) || $admin->complex_id == 0) {

        $complexes = Complex::all();
        $activities = Activity::all();
        $complexActivities = ComplexActivity::with(['complex', 'activity'])->get();

    } else {
        // ====== مدیـر مجمع محدود ======

        $complexes = Complex::where('id', $admin->complex_id)->get();

        // الأنشطة المرتبطة بالمجمع فقط
        $complexActivities = ComplexActivity::where('complex_id', $admin->complex_id)
            ->with(['complex', 'activity'])
            ->get();

        // الأنشطة المتاحة في هذا المجمع فقط
        $activities = Activity::whereIn(
            'id',
            $complexActivities->pluck('activity_id')->unique()
        )->get();
    }

    return view('admin.schedules.create', compact(
        'ageCategories',
        'complexes',
        'activities',
        'users',
        'complexActivities',
        'source'
    ));
}


    /**
     * 🔎 البحث عن أوقات متداخلة مع أفواج نشطة أخرى داخل نفس المركب
     *
     * @return array<int, string> رسائل عربية قابلة للعرض
     */
    private function findSlotConflicts(int $complexId, $timeSlots, ?int $excludeScheduleId = null): array
    {
        $newSlots = is_array($timeSlots) ? $timeSlots : json_decode((string) $timeSlots, true);

        if (!is_array($newSlots) || $newSlots === []) {
            return [];
        }

        $newSlots = $this->normalizeSlots($newSlots);

        if ($newSlots === []) {
            return [];
        }

        $complexActivityIds = ComplexActivity::where('complex_id', $complexId)->pluck('id');

        if ($complexActivityIds->isEmpty()) {
            return [];
        }

        $existing = Schedule::whereIn('complex_activity_id', $complexActivityIds)
            ->where('active', 1)
            ->whereNotNull('time_slots')
            ->when($excludeScheduleId, fn ($q) => $q->where('id', '!=', $excludeScheduleId))
            ->get(['groupe', 'time_slots']);

        $days = [
            0 => 'الأحد', 1 => 'الإثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء',
            4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت',
        ];

        $conflicts = [];

        foreach ($existing as $schedule) {
            $otherSlots = $this->normalizeSlots(json_decode($schedule->time_slots, true));

            foreach ($otherSlots as $other) {
                foreach ($newSlots as $new) {
                    if ($other['day_number'] !== $new['day_number']) {
                        continue;
                    }

                    if ($this->slotsOverlap($other['start'], $other['end'], $new['start'], $new['end'])) {
                        $conflicts[] = sprintf(
                            'تعارض في التوقيت: %s من %s إلى %s محجوز مسبقاً بالفوج «%s»',
                            $days[$new['day_number']] ?? '—',
                            $new['start'],
                            $new['end'],
                            $schedule->groupe
                        );
                    }
                }
            }
        }

        return array_values(array_unique($conflicts));
    }


    /**
     * 🧹 تطبيع أوقات الفترات: لا نقبل إلا HH:MM صحيحة
     */
    private function normalizeSlots($slots): array
    {
        if (!is_array($slots)) {
            return [];
        }

        $clean = [];

        foreach ($slots as $slot) {
            if (!is_array($slot)) {
                continue;
            }

            $day  = $slot['day_number'] ?? null;
            $start = $slot['start'] ?? null;
            $end   = $slot['end'] ?? null;

            if (!is_numeric($day) || $day < 0 || $day > 6) {
                continue;
            }

            if (! $this->isValidTime($start) || ! $this->isValidTime($end)) {
                continue;
            }

            if ($start >= $end) {
                continue;
            }

            $clean[] = [
                'day_number' => (int) $day,
                'start'      => substr($start, 0, 5),
                'end'        => substr($end, 0, 5),
            ];
        }

        return $clean;
    }


    private function isValidTime($time): bool
    {
        return is_string($time) && preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time) === 1;
    }


    /**
     * ⏱ تداخل فترة [start1, end1) مع [start2, end2) — المقارنة نصية تنفع على HH:MM
     */
    private function slotsOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        return $start1 < $end2 && $end1 > $start2;
    }


    /**
     * حفظ جدول جديد
     */


public function store(Request $request)
{
    try {

        // 🟦 1) Validation contrôlée
        $validator = Validator::make($request->all(), [
            'complex_id'   => 'required|integer',
            'activity_id'  => 'required|integer',
            'groupe'       => 'required|string|max:50',
            'sex'          => 'required|in:H,F,X',
            'nbr'          => 'nullable|integer|min:0',
          //  'type_prix'    => 'required|in:pricing_plan,fix',
            'price'        => 'required|numeric|min:0',
            'user_id'      => 'nullable|integer|exists:users,id',
            'time_slots'   => 'required|json',
            'type_season'  => 'required|string|max:50',
            'active'       => 'nullable|boolean',
            'date_debut'   => 'nullable|date',
            'date_fin'     => 'nullable|date|after_or_equal:date_debut',
        ]);
//dd(    $validator) ;
        
        $validator->after(function ($validator) use ($request) {

    if ($request->filled('nbr') && $request->filled('complex_id')) {

        $complex = Complex::find($request->complex_id);

        if ($complex && $request->nbr > $complex->capacite_ma) {

            $validator->errors()->add(
                'nbr',
                'عدد الأماكن لا يمكن أن يتجاوز سعة المركب (' . $complex->capacite_ma . ').'
            );
        }
    }

    // 🚦 لا يمكن حفظ فوج بدون أي توقيت
    if ($request->filled('time_slots')) {
        $slots = json_decode($request->time_slots, true);

        if (!is_array($slots) || $slots === []) {
            $validator->errors()->add('time_slots', '❌ اختر توقيتاً واحداً على الأقل من التقويم');
        }
    }

    // 🚦 التحقق من تداخل الأوقات — يُطبّق فقط إذا كان المركب يمنع التداخل
    if ($request->filled('complex_id') && $request->filled('time_slots')) {

        $complex = Complex::find($request->complex_id);

        if ($complex && ! $complex->allowsTimeConflict()) {

            foreach ($this->findSlotConflicts((int) $request->complex_id, $request->time_slots) as $conflict) {
                $validator->errors()->add('time_slots', '⛔ ' . $conflict);
            }
        }
    }
});


        
        
        
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // 🟦 2) récupérer complex_activity
        $complexActivity = ComplexActivity::where('complex_id', $request->complex_id)
            ->where('activity_id', $request->activity_id)
            ->first();

        if (!$complexActivity) {
            return back()->withErrors([
                'complex_id' => '❌ هذا النشاط غير مرتبط بهذا المركب'
            ])->withInput();
        }

        // 🟦 3) création schedule
        $schedule = new Schedule();
        $schedule->complex_activity_id = $complexActivity->id;
        $schedule->age_category_id     = $request->age_category_id;
        $schedule->groupe              = $request->groupe;
        $schedule->sex                 = $request->sex;
        $schedule->nbr                 = $request->nbr;
        $schedule->type_prix           = 'fix';
        $schedule->price              = $request->price ;

        $schedule->user_id             = $request->user_id;
        $schedule->time_slots          = json_encode(
            $this->normalizeSlots(json_decode($request->time_slots, true))
        );
        $schedule->type_season         = $request->type_season;
        $schedule->active              = $request->boolean('active');
        $schedule->date_debut          = $request->date_debut;
        $schedule->date_fin            = $request->date_fin;

        $schedule->save();

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', '✔ تم حفظ الجدول بنجاح');

    } catch (\Throwable $e) {

        return back()
            ->with('error', '❌ خطأ تقني أثناء الحفظ')
            ->withInput();
    }
}




    /**
     * صفحة التعديل
     */
  /*  public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);

        $ageCategories = AgeCategory::all();
        $complexes = Complex::all();
        $activities = Activity::all();
        $users = User::whereIn('type', ['club', 'company'])->get();

        // استخراج بيانات complex_id + activity_id الحالية
        $ca = ComplexActivity::find($schedule->complex_activity_id);

        $selected_complex = $ca ? $ca->complex_id : null;
        $selected_activity = $ca ? $ca->activity_id : null;

        return view('admin.schedules.edit', compact(
            'schedule',
            'ageCategories',
            'complexes',
            'activities',
            'users',
            'selected_complex',
            'selected_activity'
        ));
    }*/


public function edit($id)
{
    $admin = Auth::user();
    $schedule = Schedule::findOrFail($id);

    // استخراج بيانات complex_id و activity_id من complex_activity
    $ca = ComplexActivity::find($schedule->complex_activity_id);

    $selected_complex  = $ca ? $ca->complex_id : null;
    $selected_activity = $ca ? $ca->activity_id : null;

    // ===============================
    //     🔎 الفلاتر حسب complex
    // ===============================

    // 1️⃣ الفئات العمرية → كلها لا علاقة لها بالمجمع
    $ageCategories = AgeCategory::all();

    // 2️⃣ المجمعات
    if (is_null($admin->complex_id) || $admin->complex_id == 0) {
        // مدير عام → عرض كل المجمعات
        $complexes = Complex::all();
    } else {
        // مدير مجمع → عرض مجمع واحد فقط
        $complexes = Complex::where('id', $admin->complex_id)->get();
    }

    // 3️⃣ النشاطات
    if (is_null($admin->complex_id) || $admin->complex_id == 0) {
        // مدير عام → جميع النشاطات
        $activities = Activity::all();
    } else {
        // مدير مجمع → النشاطات المربوطة عبر complex_activity
        $activities = Activity::whereIn('id', function ($q) use ($admin) {
            $q->select('activity_id')
              ->from('complex_activity')
              ->where('complex_id', $admin->complex_id);
        })->get();
    }

    // 4️⃣ المستخدمين club/company
    if (is_null($admin->complex_id) || $admin->complex_id == 0) {
        $users = User::whereIn('type', ['club', 'company'])->get();
    } else {
        $users = User::whereIn('type', ['club', 'company'])
                     ->where('complex_id', $admin->complex_id)
                     ->get();
    }

    return view('admin.schedules.edit', compact(
        'schedule',
        'ageCategories',
        'complexes',
        'activities',
        'users',
        'selected_complex',
        'selected_activity'
    ));
}




public function occupiedSlots(Request $request)
{
    $request->validate([
        'complex_id' => 'required|integer',
        'activity_id' => 'nullable|integer',
    ]);

    $excludeSchedule = $request->integer('exclude_schedule', 0);

    $complexId = $request->complex_id;

    // 🔗 ComplexActivities du complexe (tous si pas d'activité précise)
    $query = ComplexActivity::where('complex_id', $complexId);

    if ($request->filled('activity_id')) {
        $query->where('activity_id', $request->activity_id);
    }

    $complexActivities = $query->with('activity')->get();

    if ($complexActivities->isEmpty()) {
        return response()->json([]);
    }

    // 📦 Tous les schedules ACTIFS liés à ces complex_activities
    $schedules = Schedule::whereIn('complex_activity_id', $complexActivities->pluck('id'))
        ->whereNotNull('time_slots')
        ->where('active', 1)
        ->when($excludeSchedule, fn($q) => $q->where('id', '!=', $excludeSchedule))
        ->with('complexActivity.activity')
        ->get();

    $events = [];

    foreach ($schedules as $schedule) {
        $activity = $schedule->complexActivity->activity ?? null;
        $color = $activity->color ?? '#dc3545';

        $slots = json_decode($schedule->time_slots, true);

        if (!is_array($slots)) continue;

        foreach ($slots as $slot) {

            // day_number: 0=الأحد ... 6=السبت
            // Récurrence hebdo pure (sans startRecur/endRecur) pour afficher
            // tous les groupes actifs dans la semaine affichée.
            $events[] = [
                'daysOfWeek' => [(int) $slot['day_number']],
                'startTime'  => $slot['start'],
                'endTime'    => $slot['end'],

                'display' => 'background',
                'backgroundColor' => $color,

                // 👈 Nom du groupe + activité
                'extendedProps' => [
                    'groupe'   => $schedule->groupe,
                    'activity' => $activity->title ?? '',
                    'color'    => $color,
                ],
            ];
        }
    }

    return response()->json($events);
}



public function update(Request $request, $id)
{
    try {

        // 🟦 1) Validation (نفس القواعد)
        $validator = Validator::make($request->all(), [
            'complex_id'   => 'required|integer',
            'activity_id'  => 'required|integer',
            'groupe'       => 'required|string|max:50',
            'sex'          => 'required|in:H,F,X',
            'nbr'          => 'nullable|integer|min:0',
            'price'        => 'nullable|numeric|min:0',
            'user_id'      => 'nullable|integer|exists:users,id',
            'time_slots'   => 'required|json',
            'type_season'  => 'required|string|max:50',
            'active'       => 'required|boolean',
            'date_debut'   => 'nullable|date',
            'date_fin'     => 'nullable|date|after_or_equal:date_debut',
        ]);

        // 🟦 1-bis) نفس منطقك: عدم تجاوز سعة المركب
        $validator->after(function ($validator) use ($request) {

            if ($request->filled('nbr') && $request->filled('complex_id')) {

                $complex = Complex::find($request->complex_id);

                if ($complex && $request->nbr > $complex->capacite_ma) {

                $validator->errors()->add(
                    'nbr',
                    'عدد الأماكن لا يمكن أن يتجاوز سعة المركب (' . $complex->capacite_ma . ').'
                );
            }
        }

        // 🚦 لا يمكن حفظ فوج بدون أي توقيت
        if ($request->filled('time_slots')) {
            $slots = json_decode($request->time_slots, true);

            if (!is_array($slots) || $slots === []) {
                $validator->errors()->add('time_slots', '❌ اختر توقيتاً واحداً على الأقل من التقويم');
            }
        }

        // 🚦 التحقق من تداخل الأوقات — يُطبّق فقط إذا كان المركب يمنع التداخل
        if ($request->filled('complex_id') && $request->filled('time_slots')) {

            $complex = Complex::find($request->complex_id);

            if ($complex && ! $complex->allowsTimeConflict()) {

                $conflicts = $this->findSlotConflicts(
                    (int) $request->complex_id,
                    $request->time_slots,
                    (int) $id
                );

                foreach ($conflicts as $conflict) {
                    $validator->errors()->add('time_slots', '⛔ ' . $conflict);
                }
            }
        }
    });

        // 🟦 تنفيذ التحقق
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // 🟦 نفس نتيجة request()->validate()
        $validated = $validator->validated();

        // 🟦 2) Récupération du schedule
        $schedule = Schedule::findOrFail($id);

        // 🟦 3) Vérifier le lien complex / activity
        $complexActivity = ComplexActivity::where('complex_id', $validated['complex_id'])
            ->where('activity_id', $validated['activity_id'])
            ->first();

        if (!$complexActivity) {
            return back()->withErrors([
                'complex_id' => '❌ هذا النشاط غير مرتبط بهذا المركب. يجب إضافته أولاً في complex_activities'
            ])->withInput();
        }

        // 🟦 4) Mise à jour
        $schedule->complex_activity_id = $complexActivity->id;
        $schedule->age_category_id     = $request->age_category_id;
        $schedule->groupe              = $validated['groupe'];
        $schedule->sex                 = $validated['sex'];
        $schedule->nbr                 = $validated['nbr'];
        $schedule->price               = $validated['price'];
        $schedule->user_id               = $validated['user_id'];
        $schedule->time_slots            = json_encode(
            $this->normalizeSlots(json_decode($validated['time_slots'], true))
        );
        $schedule->type_season         = $validated['type_season'];
        $schedule->active              = $request->boolean('active');
        $schedule->date_debut          = $validated['date_debut'];
        $schedule->date_fin            = $validated['date_fin'];

        $schedule->save();

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', '✔ تم تعديل الجدول بنجاح');

    } catch (\Exception $e) {

        return back()
            ->with('error', '❌ فشل تحديث الجدول: ' . $e->getMessage())
            ->withInput();
    }
}



    /**
     * تعديل الجدول
     */
 /*   public function update(Request $request, $id)
{
    try {

        // 🟦 1) Validation complète
        $validated = $request->validate([
            'complex_id'         => 'required|integer',
            'activity_id'        => 'required|integer',
           // 'age_category_id'    => 'required|integer',
            'groupe'             => 'required|string|max:50',
            'sex'                => 'required|in:H,F,X',
            'nbr'                => 'nullable|integer|min:0',
         //   'type_prix'          => 'required|in:pricing_plan,fix',
            'price'              => 'nullable|numeric|min:0',
            'user_id'            => 'nullable|integer|exists:users,id',
            'time_slots'         => 'required|json',
            'type_season'        => 'required|string|max:50',
                        'active'      => 'required|boolean',
    'date_debut'  => 'nullable|date',
    'date_fin'    => 'nullable|date|after_or_equal:date_debut',
        ]);

       
               $validated->after(function ($validator) use ($request) {

    if ($request->filled('nbr') && $request->filled('complex_id')) {

        $complex = Complex::find($request->complex_id);

        if ($complex && $request->nbr > $complex->capacite_ma) {

            $validator->errors()->add(
                'nbr',
                'عدد الأماكن لا يمكن أن يتجاوز سعة المركب (' . $complex->capacite_ma . ').'
            );
        }
    }
});

       
       
        $schedule = Schedule::findOrFail($id);

        // 🟦 2) Extraire complex_activity_id
        $complexActivity = ComplexActivity::where('complex_id', $request->complex_id)
                                          ->where('activity_id', $request->activity_id)
                                          ->first();

        if (!$complexActivity) {
            return back()->withErrors([
                'complex_id' => '❌ هذا النشاط غير مرتبط بهذا المركب. يجب إضافته أولاً في complex_activities'
            ])->withInput();
        }

        // 🟦 3) Mise à jour des champs
        $schedule->complex_activity_id = $complexActivity->id;
        $schedule->age_category_id     = $request->age_category_id;
        $schedule->groupe              = $request->groupe;
        $schedule->sex                 = $request->sex;
        $schedule->nbr                 = $request->nbr;
      //  $schedule->type_prix           = $request->type_prix;
        $schedule->price               = $request->price ; 
        $schedule->user_id             = $request->user_id;
        $schedule->time_slots          = $request->time_slots;
         $schedule->type_season              = $request->type_season;
          $schedule->active      = $request->boolean('active');
    $schedule->date_debut = $request->date_debut;
    $schedule->date_fin   = $request->date_fin;
        $schedule->save();

        return redirect()->route('admin.schedules.index')
                         ->with('success', '✔ تم تعديل الجدول بنجاح');

    } catch (\Exception $e) {

        return back()->with('error', '❌ فشل تحديث الجدول: ' . $e->getMessage())
                     ->withInput();
    }
}
*/
    /**
     * حذف جدول
     */
    public function destroy($id)
    {
        Schedule::destroy($id);

        return redirect()->route('admin.schedules.index')
                         ->with('success', '🗑 تم الحذف بنجاح');
    }
}
