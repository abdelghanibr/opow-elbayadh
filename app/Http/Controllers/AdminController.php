<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Club;
use App\Models\Person;
use App\Models\User;
use App\Models\Complex;
use App\Models\Reservation ;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        // السماح بدخول المسؤول فقط
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->type !== 'admin') {
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

$teamsCount = \App\Models\Team::count();

// ====== Complex stats (from Mila) ======
$complexStats = Complex::all()
    ->map(function($complex) {
        $userIds = User::where('complex_id', $complex->id)->pluck('id');
        $personIds = Person::whereIn('user_id', $userIds)->pluck('id');
        $approvedDossiers = Dossier::whereIn('person_id', $personIds)->where('etat', 'approved')->count();

        return (object) [
            'nom' => $complex->nom,
            'subscribers' => $personIds->count(),
            'reservations' => Reservation::whereIn('user_id', $userIds)->count(),
            'approved' => $approvedDossiers,
            'paidAmount' => 0,
        ];
    });

$complexLabels = $complexStats->pluck('nom')->toArray();
$complexReservations = $complexStats->pluck('reservations')->toArray();

// ====== Gender stats ======
$genderGlobal = [
    'ذكر' => Person::where('gender', 'H')->count() + Person::where('gender', 'M')->count(),
    'أنثى' => Person::where('gender', 'F')->count(),
    'غير محدد' => Person::whereNull('gender')->orWhere('gender', '')->count(),
];

// ====== No-dossier accounts ======
$noDossierAccountsCount = User::whereNotIn('type', ['admin'])
    ->whereDoesntHave('person', fn($q) => $q->whereNotNull('id'))
    ->count();

return view('admin.dashboard', compact(
    'personsCount',
    'clubsCount',
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
    'teamsCount',
    'complexStats',
    'complexLabels',
    'complexReservations',
    'genderGlobal',
    'noDossierAccountsCount'
));
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

    // ⚽ الفرق (Teams) عبر علاقة Team → Match → Complex
    $teamsCount = \App\Models\Team::whereHas('homeMatches', function($q) use ($id) {
        $q->where('complex_id', $id);
    })->orWhereHas('awayMatches', function($q) use ($id) {
        $q->where('complex_id', $id);
    })->count();

    $complex = Complex::findOrFail($id);

    // حساب بدون ملف
    $noDossierAccountsCount = $this->orphanAccounts()->count();

    // الأفواج النشطة
    $activeGroupsCount = \App\Models\Schedule::whereIn(
        'complex_activity_id',
        \App\Models\ComplexActivity::where('complex_id', $id)->pluck('id')
    )->where('active', 1)->count();

    return view('admin.dashboard_complex', compact(
        'complex',
        'dossiersCount',
        'clubsCount',
        'personsCount',
        'activitiesCount',
        'schedulesCount',
        'reservationsCount',
        'seatsCount',
        'matchesCount',
        'ticketsCount',
        'teamsCount',
        'noDossierAccountsCount',
        'activeGroupsCount'
    ));
}





    /**
     * 📂 عرض جميع الملفات
     */
    public function dossiersIndex()
    {
        $dossiers = Dossier::with('person.user')->latest()->get();
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
        $admins = User::where('type', 'admin')->with('complex')->get();
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
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
           'complex_id' => 'nullable|exists:complexes,id', 
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'type'       => 'admin',
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

        $request->validate([
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email,' . $admin->id,
           'complex_id' => 'nullable|exists:complexes,id', 
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
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

    public function accountsWithoutDossiers()
    {
        $orphans     = $this->orphanAccounts();
        $scopeComplex = null;

        if (!empty(Auth::user()->complex_id)) {
            $scopeComplex = Complex::find(Auth::user()->complex_id);
        }

        return view('admin.accounts.no_dossier', compact('orphans', 'scopeComplex'));
    }

    private function orphanAccounts()
    {
        $dossierPersonIds = Dossier::whereNotNull('person_id')
            ->pluck('person_id')
            ->flip()
            ->all();

        $personParent = Person::pluck('parent_id', 'id')->all();

        $childrenMap = [];
        foreach ($personParent as $pid => $parentId) {
            if ($parentId) {
                $childrenMap[$parentId][] = $pid;
            }
        }

        $rootsByUser = [];
        foreach (Person::whereNotNull('user_id')->pluck('user_id', 'id') as $pid => $userId) {
            $rootsByUser[(int) $userId][] = (int) $pid;
        }

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

    public function destroyOrphanAccount($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->id === Auth::id()) {
            return back()->with('error', '⚠ لا يمكنك حذف حسابك الخاص.');
        }

        $personIds = Person::where('user_id', $user->id)->pluck('id');

        \App\Models\Dossier::whereIn('person_id', $personIds)->delete();
        \App\Models\Reservation::where('user_id', $user->id)->delete();
        Person::where('user_id', $user->id)->delete();
        $user->delete();

        return back()->with('success', '✔ تم حذف الحساب وكل بياناته المرتبطة.');
    }

    public function destroyAllOrphanAccounts()
    {
        $orphans = $this->orphanAccounts();

        $deleted = 0;
        foreach ($orphans as $u) {
            $personIds = Person::where('user_id', $u->id)->pluck('id');
            \App\Models\Dossier::whereIn('person_id', $personIds)->delete();
            \App\Models\Reservation::where('user_id', $u->id)->delete();
            Person::where('user_id', $u->id)->delete();
            User::where('id', $u->id)->delete();
            $deleted++;
        }

        return back()->with('success', "✔ تم حذف {$deleted} حسابًا بدون ملف.");
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
}
