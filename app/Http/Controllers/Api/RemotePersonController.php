<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class RemotePersonController extends Controller
{
/*
public function index($complexId)
{
    $today = Carbon::today()->toDateString();

    $persons = DB::table('persons as p')
        ->join('users as u', 'u.id', '=', 'p.user_id')

        // ✅ JOIN COMPLEXES
        ->join('complexes as c', 'c.id', '=', 'u.complex_id')

        ->leftJoin('reservations as r', function ($join) {
            $join->on('r.user_id', '=', 'u.id');
        })
        ->where('u.complex_id', $complexId)
        ->select([
            'p.id',
            'p.firstname',
            'p.lastname',
            'p.phone as person_phone',
            'p.photo as person_photo',

            'u.phone as user_phone',
            'u.email',
            'u.complex_id',

            // ✅ NOM DU COMPLEXE
            'c.nom as complex_name',

            'p.created_at',

            // réservation
            'r.start_date',
            'r.end_date',
            'r.payment_status',
            'r.time_slots',

            // ✅ ACCESS STATUS
            DB::raw("
                CASE
                    WHEN r.payment_status = 'paid'
                     AND r.end_date IS NOT NULL
                     AND r.end_date >= ?
                    THEN 'autorisé'
                    ELSE 'non_autorisé'
                END as access_status
            "),
        ])
        ->setBindings([$today], 'select')
        ->orderByDesc('p.id')
        ->get();

    return response()->json([
        'success' => true,
        'complex' => [
            'id'   => $complexId,
            'nom' => optional($persons->first())->complex_name,
        ],
        'data'  => $persons,
        'count' => $persons->count(),
    ]);
}
*/


public function index($complexId)
{
    // 📅 اليوم - 4 أيام
    $limitDate = Carbon::today()->subDays(0)->toDateString();

    $persons = DB::table('persons as p')
        ->join('users as u', 'u.id', '=', 'p.user_id')
        ->join('complexes as c', 'c.id', '=', 'u.complex_id')

        ->join('reservations as r', function ($join) use ($limitDate) {
            $join->on('r.user_id', '=', 'u.id')
                 // ✅ فلترة مباشرة هنا
                 ->whereNotNull('r.end_date')
                 ->where('r.end_date', '>=', $limitDate);
        })

        ->where('u.complex_id', $complexId)

        ->select([
            'p.id',
            'p.firstname',
            'p.lastname',
            'p.phone as person_phone',
            'p.photo as person_photo',

            'u.phone as user_phone',
            'u.email',
            'u.complex_id',

            'c.nom as complex_name',

            'p.created_at',

            'r.start_date',
            'r.end_date',
            'r.payment_status',
            'r.time_slots',

            // 🔐 ACCESS STATUS (الآن كلهم ضمن 4 أيام)
            DB::raw("
                CASE
                    WHEN r.payment_status = 'paid'
                    THEN 'autorisé'
                    ELSE 'non_autorisé'
                END as access_status
            "),
        ])
        ->orderByDesc('p.id')
        ->get();

    return response()->json([
        'success' => true,
        'complex' => [
            'id'  => $complexId,
            'nom' => optional($persons->first())->complex_name,
        ],
        'data'  => $persons,
        'count' => $persons->count(),
    ]);
}

}
