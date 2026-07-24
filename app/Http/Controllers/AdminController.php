<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Club;
use App\Models\Person;
use App\Models\User;
use App\Models\Complex;
use App\Models\Reservation ;
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
'teamsCount'
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
        'teamsCount'
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
}
