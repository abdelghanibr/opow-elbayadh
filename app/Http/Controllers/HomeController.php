<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Event;
use App\Models\Activity;
use App\Models\MatchModel;
use App\Models\Complex;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function welcome()
    {
        $news = News::latest()
            ->take(6)
            ->get();

        $events = Event::orderBy('start_date')
            ->take(6)
            ->get();

        $activities = Activity::latest()
            ->take(6)
            ->get();

        $matchesCount = MatchModel::whereIn('status', ['scheduled', 'pending'])
            ->whereDate('match_date', '>=', Carbon::today())
            ->count();

        $upcomingMatches = MatchModel::whereIn('status', ['scheduled', 'pending'])
            ->whereDate('match_date', '>=', Carbon::today())
            ->with('homeTeam', 'awayTeam', 'complex')
            ->orderBy('match_date')
            ->limit(3)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | إحصائيات الصفحة الرئيسية
        |--------------------------------------------------------------------------
        | ملاحظة:
        | غيّر قيم type إذا كانت مختلفة في جدول complexes.
        | القيم المستعملة هنا:
        | swimming = المسابح
        | stadium  = الملاعب
        | hall     = القاعات
        */
        $stats = [
            'swimming_complexes' => Complex::where('type', 'swimming')->count(),
            'stadium_complexes'  => Complex::where('type', 'stadium')->count(),
            'hall_complexes'     => Complex::where('type', 'hall')->count(),

            'activities_count'   => Activity::count(),
            'registered_count'   => User::whereIn('type', ['person', 'club', 'company'])->count(),
            'reservations_count' => Reservation::count(),

            'news_count'         => News::where('is_active', 1)->count(),
            'events_count'       => Event::where('is_active', 1)->count(),
            'matches_count'      => $matchesCount,
        ];

        $settings = Setting::allArray();

        $wilayaAr      = $settings['wilaya_ar'] ?? 'البيض';
        $wilayaFr      = $settings['wilaya_fr'] ?? 'EL-BAYADH';
        $officeShort   = $settings['office_short'] ?? 'OPOW ' . $wilayaFr;
        $officeLabelFr = $settings['office_label_fr'] ?? 'Office du Parc Omnisports de la wilaya de ' . $wilayaFr;
        $contactEmail  = $settings['contact_email'] ?? 'contact@opow-elbayadh.dz';
        $contactPhone  = $settings['contact_phone'] ?? '049613680';
        $contactPlace  = $settings['contact_place'] ?? 'ديوان المركب المتعدد الرياضات لولاية ' . $wilayaAr;

        return view('welcome', compact(
            'news',
            'matchesCount',
            'upcomingMatches',
            'events',
            'activities',
            'stats',
            'wilayaAr',
            'wilayaFr',
            'officeShort',
            'officeLabelFr',
            'contactEmail',
            'contactPhone',
            'contactPlace'
        ));
    }


public function filter(Request $request)
{
    $type = $request->type;

    $complexes = Complex::where('type', $type)->get();

    return view('activities.complexes', compact('complexes', 'type'));
}

public function filterAjax($type)
{
    $complexes = Complex::where('type', $type)->get();

    return view('partials.complex-list', compact('complexes'));
}

    /**
     * Create a new controller instance.
     *
     * @return void
     */
   /* public function __construct()
    {
        $this->middleware('auth');
    }*/

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
}
