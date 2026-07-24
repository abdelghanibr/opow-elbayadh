<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Person;
use App\Models\Dossier;
use App\Models\Club;
use App\Models\Reservation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    



public function index()
{
    $user = Auth::user();

    // ✅ club (لا نحذفه)
    $club = Club::where('user_id', $user->id)->first();
    $dossier = \App\Models\Club::where('user_id', $user->id)->first();


    /* =====================================================
    | 🔔 ALERT : حجز مدفوع ينتهي خلال 5 أيام (قبل الانتهاء)
    ===================================================== */
    $today = Carbon::today();

    $reservationExpiring = Reservation::where('user_id', $user->id)
        ->where('payment_status', 'paid')
        ->whereDate('end_date', '>=', $today)
        ->get()
        ->map(function ($reservation) use ($today) {

            $daysRemaining = $today->diffInDays(
                Carbon::parse($reservation->end_date),
                false
            );

            if ($daysRemaining <= 5 && $daysRemaining >= 0) {
                $reservation->days_remaining = $daysRemaining;
                return $reservation;
            }

            return null;
        })
        ->filter()
        ->sortBy('days_remaining')
        ->first();

    /* =====================================================
    | 📊 Stats reservations
    ===================================================== */
    $reservationStats = $this->buildReservationStats($user->id);

    /* =====================================================
    | 📌 Dashboard CLUB
    ===================================================== */
    if ($user->type === 'club') {

        $playersCount  = Person::where('user_id', $user->id)->where('education', 'لاعب')->count();
        $coachsCount   = Person::where('user_id', $user->id)->where('education', 'مدرب')->count();
        $managersCount = Person::where('user_id', $user->id)->where('education', 'مسير')->count();

        return view('club.dashboard', [
            'user' => $user,
            'club' => $club,
            'dossier' => $dossier,
            'playersCount'  => $playersCount,
            'coachsCount'   => $coachsCount,
            'managersCount' => $managersCount,

            ...$reservationStats,
            'reservationExpiring' => $reservationExpiring,
        ]);
    }

    /* =====================================================
    | 📌 Dashboard ENTREPRISE
    ===================================================== */
    if ($user->type === 'company' || $user->type === 'entreprise') {

$playersCount  = Person::where('user_id', $user->id)->where('education', 'لاعب')->count();
        $coachsCount   = Person::where('user_id', $user->id)->where('education', 'مدرب')->count();
        $managersCount = Person::where('user_id', $user->id)->where('education', 'مسير')->count();




        return view('entreprise.dashboard', [
      'user' => $user,
            'club' => $club,
            'dossier' => $dossier,
            'playersCount'  => $playersCount,
            'coachsCount'   => $coachsCount,
            'managersCount' => $managersCount,

            ...$reservationStats,
            'reservationExpiring' => $reservationExpiring,
        ]);
    }
    /* =====================================================
    | 📌 Dashboard PERSON
    ===================================================== */
    $person = Person::where('user_id', $user->id)->first();

    $dossiers = $person
        ? Dossier::where('owner_type', 'person')->where('person_id', $person->id)->first()
        : null;

    $registeredActivities = Reservation::where('user_id', $user->id)->count();

    return view('person.dashboard', [
        'user' => $user,
        'dossier' => $dossiers,
      
        'registeredActivities' => $registeredActivities,

        ...$reservationStats,
        'reservationExpiring' => $reservationExpiring,
    ]);
}

protected function buildReservationStats($userId)
{
    $base = Reservation::where('user_id', $userId);

    return [
        'totalReservations'    => (clone $base)->count(),
        'paidReservations'     => (clone $base)->where('payment_status', 'paid')->count(),
        'pendingPayments'      => (clone $base)->where('payment_status', 'pending')->count(),
        'failedPayments'       => (clone $base)->where('payment_status', 'failed')->count(),

        'approvedReservations' => (clone $base)->where('statut', 'approved')->count(),
        'pendingReservations'  => (clone $base)->where('statut', 'pending')->count(),
        'rejectedReservations' => (clone $base)->where('statut', 'rejected')->count(),
    ];
}
    public function dashboard()
{
    return view('admin.dashboard', [
        'dossiersCount' => Dossier::count(),
        'clubsCount' => Club::count(),
        'personsCount' => Person::count()
    ]);
}


public function dashboardStats()
{
    $user = Auth::user();

    $query = Reservation::query();

    /*
    |--------------------------------------------------------------------------
    | 🔐 تصفية حسب نوع المستخدم
    |--------------------------------------------------------------------------
    */
    if ($user->type === 'club' || $user->type === 'company' || $user->type === 'entreprise') {
        // الحجوزات المرتبطة بالمؤسسة
        $query->where('user_id', $user->id);
    }

    if ($user->type === 'person') {
        // حجوزات الشخص فقط
        $query->where('user_id', $user->id);
    }

    /*
    |--------------------------------------------------------------------------
    | 📊 الإحصائيات
    |--------------------------------------------------------------------------
    */
    $stats = [
        'total'      => (clone $query)->count(),
        'paid'       => (clone $query)->where('payment_status', 'paid')->count(),
        'pending'    => (clone $query)->where('payment_status', 'pending')->count(),
        'cancelled'  => (clone $query)->where('payment_status', 'cancelled')->count(),
    ];

    return view('dashboard.index', compact('stats'));
}
}
