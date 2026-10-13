<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Club;
use App\Models\Person;
use App\Models\User;
use App\Models\Complex;
use App\Models\Reservation ;
use App\Models\Schedule;
use App\Models\ChargilyPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        // السماح بدخول المسؤول، المالي ومدقق QR
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !in_array(Auth::user()->type, ['admin', 'financier', 'verify_qr'])) {
                abort(403, 'غير مصرح لك بالدخول');
            }

            // إدارة حسابات الإدارة: المسؤول العام فقط
            if (Auth::user()->type !== 'admin'
                && str_starts_with((string) $request->route()?->getName(), 'admins.')) {
                abort(403, 'غير مصرح لك بالدخول');
            }

            return $next($request);
        });
    }

    /**
     * 🔹 لوحة التحكم
     */
public function dashboard()
    
    {
       // $personsCount   = Person::count();
        $clubsCount     = User::where('type', 'club')->count();
        $companiesCount = Club::where('entity_type', 'company')->count();
        $adminsCount    = User::where('type', 'admin')->count();
        $dossiersCount  = Dossier::count();

        // إحصائيات المجمعات
       
$reservationsParMois = Reservation::select(
        DB::raw('MONTH(created_at) as mois'),
        DB::raw('COUNT(*) as total')
    )
    ->whereYear('created_at', date('Y'))
    ->groupBy(DB::raw('MONTH(created_at)'))
    ->pluck('total', 'mois');

$chartReservations = [];
for ($i = 1; $i <= 12; $i++) {
    $chartReservations[] = $reservationsParMois[$i] ?? 0;
}
$activitiesCount = \App\Models\Activity::count();

$occupiedActivitiesCount = \App\Models\ComplexActivity::distinct('activity_id')
    ->count('activity_id');

$activitiesOccupationRate = $activitiesCount > 0
    ? round(($occupiedActivitiesCount / $activitiesCount) * 100)
    : 0;

$ageCategoriesCount = \App\Models\AgeCategory::count();


$ageCategoriesStats = \App\Models\AgeCategory::withCount('persons')->get();

$ageCategoryLabels = $ageCategoriesStats->pluck('name')->toArray();

$ageCategoryValues = $ageCategoriesStats->pluck('persons_count')->toArray();

$totalAgeRegistrations = array_sum($ageCategoryValues);


$personsCount = Person::whereHas('user', function($q) {
    $q->where('type', 'person');
})->count();

$recentDossiersCount = Dossier::whereDate('created_at', today())->count();

$recentReservationsCount = Reservation::whereDate('created_at', today())->count();

$recentTicketsCount = \App\Models\Ticket::whereDate('created_at', today())->count();

$recentEventsCount = \App\Models\Event::whereDate('created_at', today())->count();   
        
$approvedDossiersCount = Dossier::where('etat', 'approved')->count();
$rejectedDossiersCount = Dossier::where('etat', 'rejected')->count();
$pendingDossiersCount = Dossier::whereNotIn('etat', ['approved', 'rejected'])->count();
$reservationsCount = \App\Models\Reservation::count();
$paymentsCount = \App\Models\Payment::count(); // عدّل اسم الموديل إذا كان مختلفًا

$reservationRate = $personsCount > 0
    ? round(($reservationsCount / $personsCount) * 100)
    : 0;

$paymentRate = $reservationsCount > 0
    ? round(($paymentsCount / $reservationsCount) * 100)
    : 0;

$processedDossiersCount = $approvedDossiersCount + $rejectedDossiersCount;

$dossierProcessingPercent = $dossiersCount > 0
    ? round(($processedDossiersCount / $dossiersCount) * 100)
    : 0;        

$noDossierAccountsCount = $this->orphanAccounts()->count();

// =========================================================================
//   📊 إحصائيات حسب المنشآت (Souscripteurs / Réservations / Dossiers validés / Montants payés / Sexe)
// =========================================================================
extract($this->buildComplexStats());

return view('admin.dashboard', compact(
    'personsCount',
    'clubsCount',
    'companiesCount',
    'adminsCount',
    'dossiersCount',
    'chartReservations',
    'recentDossiersCount',
    'recentReservationsCount',
    'recentTicketsCount',
    'recentEventsCount',
    'dossierProcessingPercent',
'approvedDossiersCount',
'rejectedDossiersCount',
'processedDossiersCount',
'reservationsCount',
'paymentsCount',
'reservationRate',
'paymentRate',
'activitiesCount',
'occupiedActivitiesCount',
'activitiesOccupationRate',
'ageCategoriesCount',
'ageCategoryLabels',
'ageCategoryValues',
'totalAgeRegistrations',
'pendingDossiersCount',
'noDossierAccountsCount',
'complexStats',
'complexLabels',
'complexSubscribers',
'complexReservations',
'complexApproved',
'complexPaidAmounts',
'genderLabels',
'genderGlobal'
));
    }

    /**
     * 🔍 تجميع إحصائيات المنشآت (يُستعمل في اللوحة + الطباعة + PDF)
     */
    private function buildComplexStats()
    {
        // 👥 المنخرطون (subscribers) حسب المنشأة — عبر users.complex_id
        $subscribersByComplex = DB::table('persons')
            ->join('users', 'users.id', '=', 'persons.user_id')
            ->select('users.complex_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('users.complex_id')
            ->groupBy('users.complex_id')
            ->pluck('total', 'complex_id');

        // 📝 الحجوزات حسب المنشأة — عبر complex_activity
        $reservationsByComplex = DB::table('reservations')
            ->join('complex_activity', 'complex_activity.id', '=', 'reservations.complex_activity_id')
            ->select('complex_activity.complex_id', DB::raw('COUNT(*) as total'))
            ->groupBy('complex_activity.complex_id')
            ->pluck('total', 'complex_id');

        // ✅ الملفات المقبولة حسب المنشأة — عبر person → user
        $approvedDossiersByComplex = DB::table('dossiers')
            ->join('persons', 'persons.id', '=', 'dossiers.person_id')
            ->join('users', 'users.id', '=', 'persons.user_id')
            ->select('users.complex_id', DB::raw('COUNT(*) as total'))
            ->where('dossiers.etat', 'approved')
            ->whereNotNull('users.complex_id')
            ->groupBy('users.complex_id')
            ->pluck('total', 'complex_id');

        // 💰 المبالغ المدفوعة حسب المنشأة — réservations payées
        $paidAmountsByComplex = DB::table('reservations')
            ->join('complex_activity', 'complex_activity.id', '=', 'reservations.complex_activity_id')
            ->select('complex_activity.complex_id', DB::raw('COALESCE(SUM(reservations.total_price),0) as total'))
            ->where('reservations.payment_status', 'paid')
            ->groupBy('complex_activity.complex_id')
            ->pluck('total', 'complex_id');

        // 🧑‍🤝‍🧑 Sexe par المنشأة + répartition globale
        $genderByComplex = DB::table('persons')
            ->join('users', 'users.id', '=', 'persons.user_id')
            ->select('users.complex_id', 'persons.gender', DB::raw('COUNT(*) as total'))
            ->whereNotNull('users.complex_id')
            ->groupBy('users.complex_id', 'persons.gender')
            ->get();

        $genderLabels = ['ذكور', 'إناث', 'غير محدد'];
        $genderGlobal = ['ذكر' => 0, 'أنثى' => 0, 'غير محدد' => 0];

        // 🔢 تسوية قيم الجنس (H/M/ذكر → ذكر، F/أنثى → أنثى)
        foreach ($genderByComplex as $g) {
            $raw = trim((string) $g->gender);
            $key = in_array($raw, ['H', 'M', 'ذكر', 'Male', 'Homme', 'm', 'h']) ? 'ذكر'
                : (in_array($raw, ['F', 'أنثى', 'Female', 'Femme', 'f']) ? 'أنثى' : 'غير محدد');
            $genderGlobal[$key] += $g->total;
        }

        // 👶 الفئات العمرية حسب المنشأة
        $ageByComplex = DB::table('persons')
            ->join('users', 'users.id', '=', 'persons.user_id')
            ->select('users.complex_id', 'persons.age_category_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('users.complex_id')
            ->groupBy('users.complex_id', 'persons.age_category_id')
            ->get();

        $ageCategories = \App\Models\AgeCategory::orderBy('id')->get();

        // 🏗️ Assemblage des stats par المنشأة
        $complexStats = \App\Models\Complex::orderBy('nom')->get()->map(function ($c) use (
            $subscribersByComplex,
            $reservationsByComplex,
            $approvedDossiersByComplex,
            $paidAmountsByComplex,
            $genderByComplex,
            $ageByComplex,
            $ageCategories
        ) {
            $cid = $c->id;

            $gender = ['ذكر' => 0, 'أنثى' => 0, 'غير محدد' => 0];
            foreach ($genderByComplex as $g) {
                if ((int) $g->complex_id !== $cid) continue;
                $raw = trim((string) $g->gender);
                $key = in_array($raw, ['H', 'M', 'ذكر', 'Male', 'Homme', 'm', 'h']) ? 'ذكر'
                    : (in_array($raw, ['F', 'أنثى', 'Female', 'Femme', 'f']) ? 'أنثى' : 'غير محدد');
                $gender[$key] += $g->total;
            }

            $age = [];
            foreach ($ageCategories as $ac) {
                $age[$ac->id] = 0;
            }
            foreach ($ageByComplex as $a) {
                if ((int) $a->complex_id === $cid && isset($age[$a->age_category_id])) {
                    $age[$a->age_category_id] += $a->total;
                }
            }

            return (object) [
                'nom'             => $c->nom,
                'subscribers'     => (int) ($subscribersByComplex[$cid] ?? 0),
                'reservations'    => (int) ($reservationsByComplex[$cid] ?? 0),
                'approved'        => (int) ($approvedDossiersByComplex[$cid] ?? 0),
                'paidAmount'      => (float) ($paidAmountsByComplex[$cid] ?? 0),
                'gender'          => $gender,
                'age'             => $age,
            ];
        });

        $complexLabels        = $complexStats->pluck('nom')->map(fn($n) => mb_strimwidth($n, 0, 24, '…'))->values();
        $complexSubscribers   = $complexStats->pluck('subscribers')->values();
        $complexReservations  = $complexStats->pluck('reservations')->values();
        $complexApproved      = $complexStats->pluck('approved')->values();
        $complexPaidAmounts   = $complexStats->pluck('paidAmount')->values();

        return compact(
            'complexStats',
            'complexLabels',
            'complexSubscribers',
            'complexReservations',
            'complexApproved',
            'complexPaidAmounts',
            'genderLabels',
            'genderGlobal'
        );
    }

    /**
     * 🖨️ طباعة تفاصيل المنشآت (المتصفح يتيح الحفظ كـ PDF أيضًا)
     */
    public function printComplexes()
    {
        $data = $this->buildComplexStats();
        $data['printedAt'] = now();

        return view('admin.complexes.print', $data);
    }

public function dashboardComplex($id)
{
    $admin = Auth::user();

    // 🚨 منع الدخول لمجمع آخر
    if ($admin->complex_id != $id) {
        abort(403, 'غير مصرح لك بدخول هذا المجمع');
    }

    // ✨ دُوسييه عبر علاقة Person → User
    $dossiersCount = Dossier::whereHas('person.user', function ($q) use ($id) {
        $q->where('complex_id', $id);
    })->count();

    // 🧍‍♂️ الأشخاص عبر علاقة User
    $personsCount = Person::whereHas('user', function ($q) use ($id) {
        $q->where('complex_id', $id);
    })->count();

    // 🏊 النوادي عبر علاقة User
    $clubsCount = Club::whereHas('user', function ($q) use ($id) {
        $q->where('complex_id', $id);
    })->count();

    // 🏢 المؤسسات عبر علاقة User
    $companiesCount = Club::where('entity_type', 'company')->whereHas('user', function ($q) use ($id) {
        $q->where('complex_id', $id);
    })->count();

    // 🏋️ الأنشطة المخصصة للمجمع
    $activitiesCount = \App\Models\ComplexActivity::where('complex_id', $id)->count();

    // ⏰ الجداول الزمنية
    $schedulesCount = \App\Models\Schedule::whereIn(
        'complex_activity_id',
        \App\Models\ComplexActivity::where('complex_id', $id)->pluck('id')
    )->count();

    // 📝 الحجوزات الخاصة بالمجمّع عبر علاقة User → Complex
    $reservationsCount = Reservation::whereHas('user', function ($q) use ($id) {
        $q->where('complex_id', $id);
    })->count();

    // 🪑 المقاعد (ComplexSeat) لهذا المجمع
    $seatsCount = \App\Models\ComplexSeat::where('complex_id', $id)->count();

    // 🎮 المباريات (Matches) لهذا المجمع
    $matchesCount = \App\Models\MatchModel::where('complex_id', $id)->count();

    // 🎫 التذاكر (Tickets) عبر علاقة Ticket → Match → Complex
    $ticketsCount = \App\Models\Ticket::whereHas('match', function($q) use ($id) {
        $q->where('complex_id', $id);
    })->count();

    $complex = Complex::findOrFail($id);

    $noDossierAccountsCount = $this->orphanAccounts()->count();

    // 📅 Compteur des groupes actifs (tuile « البرنامج الأسبوعي للمنشأة »)
    $activeGroupsCount = \App\Models\Schedule::whereIn(
        'complex_activity_id',
        \App\Models\ComplexActivity::where('complex_id', $id)->pluck('id')
    )->where('active', 1)->count();

    return view('admin.dashboard_complex', compact(
        'complex',
        'dossiersCount',
        'clubsCount',
        'companiesCount',
        'noDossierAccountsCount',
        'personsCount',
        'activitiesCount',
        'schedulesCount',
        'reservationsCount',
        'seatsCount',
        'matchesCount',
        'ticketsCount',
        'activeGroupsCount'
    ));
}

public function programmeHebdo(Request $request, $id)
{
    $admin = Auth::user();

    if ($admin->complex_id != $id) {
        abort(403, 'غير مصرح لك بدخول هذا المجمع');
    }

    $complex = Complex::findOrFail($id);

    $complexActivityIds = \App\Models\ComplexActivity::where('complex_id', $id)->pluck('id');

    $activeSchedules = \App\Models\Schedule::whereIn('complex_activity_id', $complexActivityIds)
        ->where('active', 1)
        ->with('complexActivity.activity')
        ->get();

    $reservationCounts = \App\Models\Reservation::whereIn('schedule_id', $activeSchedules->pluck('id'))
        ->where('statut', '!=', 'annulee')
        ->selectRaw('schedule_id, COUNT(*) as total')
        ->groupBy('schedule_id')
        ->pluck('total', 'schedule_id');

    $weekStart = $request->query('start')
        ? \Carbon\Carbon::parse($request->query('start'))->startOfWeek(\Carbon\Carbon::SUNDAY)
        : now()->startOfWeek(\Carbon\Carbon::SUNDAY);

    $weekDays = [];
    for ($i = 0; $i < 7; $i++) {
        $weekDays[$i] = $weekStart->copy()->addDays($i);
    }

    $prevWeek = $weekStart->copy()->subDays(7)->format('Y-m-d');
    $nextWeek = $weekStart->copy()->addDays(7)->format('Y-m-d');

    $calendarDays = collect(range(0, 6))->mapWithKeys(function ($day) {
        return [$day => collect()];
    })->all();

    foreach ($activeSchedules as $s) {
        $slots = $s->time_slots;
        if (is_string($slots)) {
            $slots = json_decode($slots, true);
        }
        if (!is_array($slots)) {
            $slots = [];
        }
        foreach ($slots as $slot) {
            $day = $slot['day_number'] ?? null;
            if ($day === null) {
                continue;
            }
            $calendarDays[$day]->push((object) [
                'schedule'       => $s,
                'start'          => $slot['start'] ?? '',
                'end'            => $slot['end'] ?? '',
                'color'          => $s->complexActivity->activity->color ?? '#0ea5e9',
                'activity_title' => $s->complexActivity->activity->title ?? '—',
                'reservations'   => $reservationCounts[$s->id] ?? 0,
            ]);
        }
    }

    foreach ($calendarDays as $day => $items) {
        $calendarDays[$day] = $items->sortBy('start')->values();
    }

    return view('admin.programme_hebdo', compact(
        'complex',
        'activeSchedules',
        'weekDays',
        'calendarDays',
        'prevWeek',
        'nextWeek',
        'weekStart'
    ));
}





    /**
     * 📂 عرض جميع الملفات
     */
    public function dossiersIndex()
    {
        $dossiers = Dossier::with('person.user', 'person.complex')->latest()->get();
        return view('admin.dossiers.index', compact('dossiers'));
    }

    /**
     * ✔ قبول ملف
     */
    public function approveDossier($id)
    {
        $d = Dossier::findOrFail($id);
        $d->etat = 'approved';
        $d->note_admin = 'تم القبول من قبل الإدارة';
        $d->save();

        return redirect()->back()->with('success', 'تم قبول الملف بنجاح ✔');
    }

    /**
     * ❌ رفض ملف
     */
    public function rejectDossier($id)
    {
        $d = Dossier::findOrFail($id);
        $d->etat = 'rejected';
        $d->note_admin = 'تم الرفض من قبل الإدارة';
        $d->save();

        return redirect()->back()->with('error', 'تم رفض الملف ❌');
    }

    /**
     * 🏊‍♂️ عرض قائمة النوادي
     */
    public function clubsIndex(Request $request)
    {
        $query = Club::with(['user', 'user.complex']);

        if ($request->complex_id) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('complex_id', $request->complex_id);
            });
        }

        $clubs = $query->latest()->get();
        $complexes = Complex::orderBy('nom')->get();

        return view('admin.clubs.index', compact('clubs', 'complexes'));
    }

    /**
     * 👥 عرض جميع الأفراد
     */
    public function personsIndex(Request $request)
    {
        $query = Person::with(['user', 'user.complex']);

        if ($request->complex_id) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('complex_id', $request->complex_id);
            });
        }

        $persons = $query->latest()->get();
        $complexes = Complex::orderBy('nom')->get();

        return view('admin.persons.index', compact('persons', 'complexes'));
    }

    /**
     * 👮‍♂️ عرض قائمة المسؤولين
     */
    public function adminsIndex()
    {
        $admins = User::whereIn('type', ['admin', 'verify_qr', 'financier'])->with('complex')->get();
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * ➕ صفحة إنشاء مسؤول جديد
     */
    public function adminsCreate()
    {
        $complexes = Complex::orderBy('nom')->get();
        return view('admin.admins.create', compact('complexes'));
    }

    /**
     * 💾 حفظ مسؤول جديد
     */
    public function adminsStore(Request $request)
    {
        $type = in_array($request->input('type', 'admin'), ['verify_qr', 'financier']) ? $request->input('type') : 'admin';

        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'complex_id' => $type === 'verify_qr' ? 'required|exists:complexes,id' : 'nullable|exists:complexes,id',
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'type'       => $type,
            'complex_id' => $request->complex_id
        ]);

        return redirect()->route('admins.index')->with('success', 'تم إضافة المسؤول بنجاح');
    }

    /**
     * ✏️ صفحة تعديل مسؤول
     */
    public function adminsEdit($id)
    {
        $admin = User::findOrFail($id);
        $complexes = Complex::orderBy('nom')->get();

        return view('admin.admins.edit', compact('admin', 'complexes'));
    }

    /**
     * 🔄 تحديث بيانات المسؤول
     */
    public function adminsUpdate(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $type = in_array($request->input('type', 'admin'), ['verify_qr', 'financier']) ? $request->input('type') : 'admin';

        $request->validate([
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email,' . $admin->id,
            'complex_id' => $type === 'verify_qr' ? 'required|exists:complexes,id' : 'nullable|exists:complexes,id',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->type = $type;
        $admin->complex_id = $request->complex_id;

        if ($request->password) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->route('admins.index')->with('success', 'تم تحديث بيانات المسؤول');
    }

    /**
     * 🗑 حذف مسؤول
     */
    public function adminsDelete($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->back()->with('success', 'تم حذف المسؤول');
    }

    /* =========================================================================
       👥 الحسابات بدون ملف (person / club / company) — تنظيف الحسابات غير المكتملة
    ========================================================================= */

    public function accountsWithoutDossiers()
    {
        $orphans     = $this->orphanAccounts();
        $scopeComplex = null;

        if (!empty(Auth::user()->complex_id)) {
            $scopeComplex = Complex::find(Auth::user()->complex_id);
        }

        return view('admin.accounts.no_dossier', compact('orphans', 'scopeComplex'));
    }

    /**
     * حساب واحد: بدون أي ملف في شجرة أشخاصه
     */
    public function destroyOrphanAccount(User $user)
    {
        if (!in_array($user->type, ['person', 'club', 'company']) || (int) $user->id === (int) Auth::id()) {
            return back()->with('error', 'لا يمكن حذف هذا الحساب.');
        }

        $this->ensureOrphanInScope($user);

        try {
            $files = [];
            DB::transaction(function () use ($user, &$files) {
                $files = $this->deleteUserAccount($user);
            });
            $this->deletePhysicalFiles($files);
        } catch (\Throwable $e) {
            return back()->with('error', 'تعذر حذف الحساب: ' . $e->getMessage());
        }

        return back()->with('success', "تم حذف الحساب «{$user->email}» نهائيًا ✔");
    }

    /**
     * حذف كل الحسابات بدون ملف دفعة واحدة
     */
    public function destroyAllOrphanAccounts()
    {
        $orphans = $this->orphanAccounts();

        if ($orphans->isEmpty()) {
            return back()->with('error', 'لا توجد حسابات بدون ملف.');
        }

        $deleted = 0;
        $failed  = 0;

        foreach ($orphans as $user) {
            try {
                $files = [];
                DB::transaction(function () use ($user, &$files) {
                    $files = $this->deleteUserAccount($user);
                });
                $this->deletePhysicalFiles($files);
                $deleted++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        $msg = "تم حذف {$deleted} حساب نهائيًا ✔";
        if ($failed > 0) {
            $msg .= " — فشل حذف {$failed} حساب.";
        }

        return back()->with($failed > 0 ? 'warning' : 'success', $msg);
    }

    /**
     * الحسابات (person/club/company) التي لا تملك أي ملف
     * في كل شجرة الأشخاص التابعة لها (نفسها + أبنائها المتسلسلين).
     */
    private function orphanAccounts()
    {
        $dossierPersonIds = Dossier::whereNotNull('person_id')
            ->pluck('person_id')
            ->flip()
            ->all();

        // id الشخص => id الأب (كلها في الذاكرة لتجنب N+1)
        $personParent = Person::pluck('parent_id', 'id')->all();

        // خريطة الأطفال: parent_id => [ids]
        $childrenMap = [];
        foreach ($personParent as $pid => $parentId) {
            if ($parentId) {
                $childrenMap[$parentId][] = $pid;
            }
        }

        // جذور الحساب: user_id => [root person ids]
        $rootsByUser = [];
        foreach (Person::whereNotNull('user_id')->pluck('user_id', 'id') as $pid => $userId) {
            $rootsByUser[(int) $userId][] = (int) $pid;
        }

        // 🎯 نطاق المجمع: لو أن المدير مرتبط بمجمع، نعرض فقط حسابات هذا المجمع
        $complexId = Auth::user()->complex_id ?? 0;

        $users = User::whereIn('type', ['person', 'club', 'company'])
            ->where('id', '!=', Auth::id())
            ->when(!empty($complexId), function ($q) use ($complexId) {
                $q->where('complex_id', $complexId);
            })
            ->orderBy('id')
            ->get(['id', 'name', 'email', 'phone', 'type', 'created_at']);

        $orphans = [];
        foreach ($users as $u) {
            $roots = $rootsByUser[(int) $u->id] ?? [];
            $hasDossier = false;

            foreach ($roots as $root) {
                $stack = [$root];
                $seen  = [];

                while ($stack) {
                    $cur = array_pop($stack);
                    if (isset($seen[$cur])) {
                        continue;
                    }
                    $seen[$cur] = true;

                    if (isset($dossierPersonIds[$cur])) {
                        $hasDossier = true;
                        break 2;
                    }

                    foreach ($childrenMap[$cur] ?? [] as $child) {
                        $stack[] = $child;
                    }
                }
            }

            if (!$hasDossier) {
                $orphans[] = $u;
            }
        }

        return collect($orphans);
    }

    /**
     * 🚫 منع مدير مجمع من حذف حساب من مجمع آخر
     */
    private function ensureOrphanInScope(User $user)
    {
        $complexId = Auth::user()->complex_id ?? 0;

        if (!empty($complexId) && (int) ($user->complex_id ?? 0) !== (int) $complexId) {
            abort(403, 'لا يمكنك حذف حساب من مجمع آخر.');
        }
    }

    /**
     * جميع أشخاص الحساب: الجذور (user_id = user) + أحفادهم عبر parent_id.
     */
    private function allPersonIdsForUser(User $user)
    {
        $roots = Person::where('user_id', $user->id)->pluck('id');
        $ids   = $roots->toArray();
        $queue = $roots;

        while ($queue->isNotEmpty()) {
            $children = Person::whereIn('parent_id', $queue)->pluck('id');
            $ids      = array_merge($ids, $children->all());
            $queue    = $children;
        }

        return collect(array_unique($ids))->values();
    }

    /**
     * حذف حساب + كل ما يخصه. يُرجع قائمة الملفات الفعلية لحذفها بعد الـ commit.
     */
    private function deleteUserAccount(User $user)
    {
        $personIds = $this->allPersonIdsForUser($user);

        // 📂 الملفات (dossiers) + مسارات المرفقات
        $files = [];
        $dossiers = Dossier::whereIn('person_id', $personIds)->get();

        foreach ($dossiers as $d) {
            $atts = json_decode($d->attachments, true);
            if (is_array($atts)) {
                foreach ($atts as $f) {
                    if (is_string($f) && $f !== '') {
                        $files[] = $f;
                    }
                }
            }
        }

        $dossiers->each->delete();

        // 📝 الحجوزات
        Reservation::whereIn('person_id', $personIds)
            ->orWhere('user_id', $user->id)
            ->delete();

        // 🔗 إبطال المراجع الاختيارية نحو المستخدم
        Schedule::where('user_id', $user->id)->update(['user_id' => null]);
        Reservation::where('updated_by', $user->id)->update(['updated_by' => null]);

        // 💳 مدفوعات المستخدم
        \App\Models\ChargilyPayment::where('user_id', $user->id)->delete();

        // 🏊 نادي المستخدم
        Club::where('user_id', $user->id)->delete();

        // 👥 الأشخاص (الجذور + الأبناء)
        Person::whereIn('id', $personIds)->delete();

        // 🔐 الحساب نفسه
        $user->delete();

        return $files;
    }

    private function deletePhysicalFiles(array $files)
    {
        foreach ($files as $path) {
            $full = public_path($path);
            if ($full !== false && file_exists($full)) {
                @unlink($full);
            }
        }
    }
}
