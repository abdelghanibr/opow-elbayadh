<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\PersonAssurance;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonAssuranceController extends Controller
{
    private function assignedComplexId(): ?int
    {
        $user = Auth::user();

        if (!$user || empty($user->complex_id) || (int) $user->complex_id === 0) {
            return null;
        }

        return (int) $user->complex_id;
    }

    private function requestedOrAssignedComplexId(Request $request): ?int
    {
        $assignedComplexId = $this->assignedComplexId();

        if ($assignedComplexId) {
            return $assignedComplexId;
        }

        if ($request->filled('complex_id') && $request->complex_id !== 'all') {
            return (int) $request->complex_id;
        }

        return null;
    }

    private function applyComplexScopeToAssuranceQuery($query, ?int $complexId = null)
    {
        $complexId = $complexId ?: $this->assignedComplexId();

        if ($complexId) {
            $query->where('users.complex_id', $complexId);
        }

        return $query;
    }

    private function scopeAssuranceEloquent($query)
    {
        $complexId = $this->assignedComplexId();

        if ($complexId) {
            $query->whereHas('person', function ($personQuery) use ($complexId) {
                $personQuery->whereHas('user', function ($userQuery) use ($complexId) {
                    $userQuery->where('complex_id', $complexId);
                });
            });
        }

        return $query;
    }

    private function scopeReservationEloquent($query)
    {
        $complexId = $this->assignedComplexId();

        if ($complexId) {
            $query->whereExists(function ($q) use ($complexId) {
                $q->selectRaw(1)
                    ->from('users')
                    ->whereColumn('users.id', 'reservations.user_id')
                    ->where('users.complex_id', $complexId);
            });
        }

        return $query;
    }

    public function index()
    {
        $this->updateExpiredAssurances();

        $assignedComplexId = $this->assignedComplexId();

        $complexesQuery = DB::table('complexes')->select('id', 'nom')->orderBy('nom');

        if ($assignedComplexId) {
            $complexesQuery->where('id', $assignedComplexId);
        }

        $complexes = $complexesQuery->get();

        $statsBase = DB::table('person_assurances')
            ->leftJoin('persons', 'persons.id', '=', 'person_assurances.person_id')
            ->leftJoin('users', 'users.id', '=', 'persons.user_id');

        $this->applyComplexScopeToAssuranceQuery($statsBase, $assignedComplexId);

        $stats = [
            'total' => (clone $statsBase)->count(),
            'pending' => (clone $statsBase)->where('person_assurances.status', 'pending')->count(),
            'assured' => (clone $statsBase)->where('person_assurances.status', 'assured')->count(),
            'expired' => (clone $statsBase)->where('person_assurances.status', 'expired')->count(),
            'cancelled' => (clone $statsBase)->where('person_assurances.status', 'cancelled')->count(),
        ];

        return view('admin.assurances.index', compact('complexes', 'stats', 'assignedComplexId'));
    }

    private function updateExpiredAssurances(): void
    {
        $today = now()->toDateString();

        $expiredIds = PersonAssurance::whereIn('status', ['pending', 'assured'])
            ->whereDate('end_date', '<', $today)
            ->pluck('id');

        if ($expiredIds->isEmpty()) {
            return;
        }

        PersonAssurance::whereIn('id', $expiredIds)->update([
            'status' => 'expired',
            'updated_at' => now(),
        ]);

        $personIds = PersonAssurance::whereIn('id', $expiredIds)
            ->pluck('person_id')
            ->unique();

        foreach ($personIds as $personId) {
            $this->syncPersonCurrentAssurance((int) $personId);
        }
    }

    public function data(Request $request)
    {
        try {
            $this->updateExpiredAssurances();

            $columns = [
                0 => 'person_assurances.id',
                1 => 'person_assurances.id',
                2 => 'person_assurances.created_at',
                3 => 'persons.firstname',
                4 => 'persons.lastname',
                5 => 'persons.phone',
                6 => 'persons.birth_date',
                7 => 'complexes.nom',
                8 => 'reservations.start_date',
                9 => 'reservations.end_date',
                10 => 'person_assurances.start_date',
                11 => 'person_assurances.end_date',
                12 => 'person_assurances.status',
                13 => 'person_assurances.operation_type',
                14 => 'person_assurances.id',
            ];

            $query = DB::table('person_assurances')
                ->leftJoin('persons', 'persons.id', '=', 'person_assurances.person_id')
                ->leftJoin('reservations', 'reservations.id', '=', 'person_assurances.reservation_id')
                ->leftJoin('users', 'users.id', '=', 'persons.user_id')
                ->leftJoin('complexes', 'complexes.id', '=', 'users.complex_id')
                ->select([
                    'person_assurances.id',
                    'person_assurances.person_id',
                    'person_assurances.reservation_id',
                    'person_assurances.start_date as assurance_start_date',
                    'person_assurances.end_date as assurance_end_date',
                    'person_assurances.status',
                    'person_assurances.operation_type',
                    'person_assurances.source',
                    'person_assurances.created_at',
                    'person_assurances.printed_at',
                    'persons.firstname',
                    'persons.lastname',
                    'persons.phone',
                    'persons.birth_date',
                    'reservations.start_date as reservation_start_date',
                    'reservations.end_date as reservation_end_date',
                    'complexes.nom as complex_name',
                ]);

            $permissionComplexId = $this->requestedOrAssignedComplexId($request);
            $this->applyComplexScopeToAssuranceQuery($query, $permissionComplexId);

            $recordsTotalQuery = DB::table('person_assurances')
                ->leftJoin('persons', 'persons.id', '=', 'person_assurances.person_id')
                ->leftJoin('users', 'users.id', '=', 'persons.user_id');
            $this->applyComplexScopeToAssuranceQuery($recordsTotalQuery, $permissionComplexId);
            $recordsTotal = $recordsTotalQuery->count();

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('person_assurances.status', $request->status);
            }

            if ($request->filled('operation_type') && $request->operation_type !== 'all') {
                $query->where('person_assurances.operation_type', $request->operation_type);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('person_assurances.start_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('person_assurances.end_date', '<=', $request->end_date);
            }

            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('persons.firstname', 'like', "%{$search}%")
                        ->orWhere('persons.lastname', 'like', "%{$search}%")
                        ->orWhere('persons.phone', 'like', "%{$search}%")
                        ->orWhere('complexes.nom', 'like', "%{$search}%")
                        ->orWhere('person_assurances.status', 'like', "%{$search}%")
                        ->orWhere('person_assurances.operation_type', 'like', "%{$search}%")
                        ->orWhere('person_assurances.id', $search);
                });
            }

            $recordsFiltered = (clone $query)->count();

            $orderColumnIndex = (int) $request->input('order.0.column', 1);
            $orderDirection = $request->input('order.0.dir', 'desc');
            $orderColumn = $columns[$orderColumnIndex] ?? 'person_assurances.id';
            $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 25);
            $length = $length > 0 ? $length : 25;

            $rows = $query
                ->orderBy($orderColumn, $orderDirection)
                ->offset($start)
                ->limit($length)
                ->get();

            $data = $rows->map(function ($row) {
                return [
                    'checkbox' => '<input type="checkbox" class="assurance-check" value="' . e($row->id) . '">',
                    'id' => $row->id,
                    'created_at' => $this->formatDateTime($row->created_at),
                    'firstname' => e($row->firstname ?? ''),
                    'lastname' => e($row->lastname ?? ''),
                    'phone' => e($row->phone ?? '---'),
                    'birth_date' => $this->formatDate($row->birth_date),
                    'complex' => e($row->complex_name ?? '---'),
                    'reservation_period' => $this->formatDate($row->reservation_start_date) . ' - ' . $this->formatDate($row->reservation_end_date),
                    'assurance_period' => $this->formatDate($row->assurance_start_date) . ' - ' . $this->formatDate($row->assurance_end_date),
                    'status' => $this->statusBadge($row->status),
                    'operation_type' => $this->operationLabel($row->operation_type),
                    'actions' => '<button type="button" class="ass-mini-btn" disabled>محفوظ</button>',
                ];
            })->values();

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('Assurances DataTable error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage() . ' ligne ' . $e->getLine(),
            ], 500);
        }
    }

    public function candidatesData(Request $request)
    {
        try {
            $today = now()->toDateString();
            $permissionComplexId = $this->requestedOrAssignedComplexId($request);

            $columns = [
                0 => 'reservations.id',
                1 => 'persons.firstname',
                2 => 'persons.lastname',
                3 => 'persons.phone',
                4 => 'persons.birth_date',
                5 => 'complexes.nom',
                6 => 'reservations.start_date',
                7 => 'reservations.end_date',
                8 => 'reservations.created_at',
                9 => 'reservations.id',
            ];

            $query = DB::table('reservations')
                ->join('persons', 'persons.user_id', '=', 'reservations.user_id')
                ->leftJoin('users', 'users.id', '=', 'persons.user_id')
                ->leftJoin('complexes', 'complexes.id', '=', 'users.complex_id')
                ->leftJoin('person_assurances', function ($join) {
                    $join->on('person_assurances.person_id', '=', 'persons.id')
                        ->on('person_assurances.start_date', '=', 'reservations.start_date')
                        ->on('person_assurances.end_date', '=', 'reservations.end_date')
                        ->where('person_assurances.status', '<>', 'cancelled');
                })
                ->where('reservations.payment_status', 'paid')
                ->whereDate('reservations.end_date', '>=', $today)
                ->whereNull('person_assurances.id')
                ->select([
                    'reservations.id as reservation_id',
                    'reservations.start_date',
                    'reservations.end_date',
                    'reservations.created_at as reservation_created_at',
                    'persons.id as person_id',
                    'persons.firstname',
                    'persons.lastname',
                    'persons.phone',
                    'persons.birth_date',
                    'complexes.nom as complex_name',
                ]);

            if ($permissionComplexId) {
                $query->where('users.complex_id', $permissionComplexId);
            }

            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('persons.firstname', 'like', "%{$search}%")
                        ->orWhere('persons.lastname', 'like', "%{$search}%")
                        ->orWhere('persons.phone', 'like', "%{$search}%")
                        ->orWhere('complexes.nom', 'like', "%{$search}%")
                        ->orWhere('reservations.id', $search);
                });
            }

            $recordsTotal = (clone $query)->count();
            $recordsFiltered = $recordsTotal;

            $orderColumnIndex = (int) $request->input('order.0.column', 8);
            $orderDirection = $request->input('order.0.dir', 'desc');
            $orderColumn = $columns[$orderColumnIndex] ?? 'reservations.id';
            $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $length = $length > 0 ? $length : 10;

            $rows = $query
                ->orderBy($orderColumn, $orderDirection)
                ->offset($start)
                ->limit($length)
                ->get();

            $data = $rows->map(function ($row) {
                $operation = $this->hasPreviousAssurance((int) $row->person_id) ? 'renewal' : 'new';

                return [
                    'checkbox' => '<input type="checkbox" class="candidate-check" value="' . e($row->reservation_id) . '">',
                    'firstname' => e($row->firstname ?? ''),
                    'lastname' => e($row->lastname ?? ''),
                    'phone' => e($row->phone ?? '---'),
                    'birth_date' => $this->formatDate($row->birth_date),
                    'complex' => e($row->complex_name ?? '---'),
                    'reservation_period' => $this->formatDate($row->start_date) . ' - ' . $this->formatDate($row->end_date),
                    'operation_type' => $this->operationLabel($operation),
                    'reservation_created_at' => $this->formatDateTime($row->reservation_created_at),
                    'reservation_id' => $row->reservation_id,
                ];
            })->values();

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('Assurances candidates DataTable error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage() . ' ligne ' . $e->getLine(),
            ], 500);
        }
    }

    public function storeSelected(Request $request)
    {
        $request->validate([
            'reservation_ids' => 'required|array',
            'reservation_ids.*' => 'integer|exists:reservations,id',
        ]);

        $created = 0;
        $skipped = 0;
        $today = now()->toDateString();

        $reservationsQuery = Reservation::query()
            ->whereIn('id', $request->reservation_ids)
            ->where('payment_status', 'paid')
            ->whereDate('end_date', '>=', $today);

        $this->scopeReservationEloquent($reservationsQuery);

        $reservations = $reservationsQuery->get();

        foreach ($reservations as $reservation) {
            $person = Person::where('user_id', $reservation->user_id)->first();

            if (!$person || !$reservation->start_date || !$reservation->end_date) {
                $skipped++;
                continue;
            }

            $exists = PersonAssurance::where('person_id', $person->id)
                ->whereDate('start_date', $reservation->start_date)
                ->whereDate('end_date', $reservation->end_date)
                ->where('status', '<>', 'cancelled')
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $operationType = $this->hasPreviousAssurance((int) $person->id) ? 'renewal' : 'new';

            $assurance = PersonAssurance::create([
                'person_id' => $person->id,
                'reservation_id' => $reservation->id,
                'start_date' => $reservation->start_date,
                'end_date' => $reservation->end_date,
                'status' => 'pending',
                'operation_type' => $operationType,
                'source' => 'manual',
                'created_by' => Auth::id(),
                'note' => 'Ajout manuel depuis popup des réservations actives payées.',
            ]);

            $person->update([
                'etat_ass' => 0,
                'assurance_status' => 'pending',
                'assurance_start_date' => $assurance->start_date,
                'assurance_end_date' => $assurance->end_date,
                'assured_by' => Auth::id(),
                'assured_at' => now(),
            ]);

            $created++;
        }

        return back()->with('success', "تمت إضافة {$created} سجل تأمين. تم تجاهل {$skipped} سجل مكرر أو غير صالح.");
    }

    public function bulkAssure(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:person_assurances,id',
        ]);

        $assurancesQuery = PersonAssurance::whereIn('id', $request->ids)
            ->where('status', 'pending');

        $this->scopeAssuranceEloquent($assurancesQuery);

        $assurances = $assurancesQuery->get();

        foreach ($assurances as $assurance) {
            $assurance->update([
                'status' => 'assured',
                'assured_at' => now(),
            ]);

            $this->syncPersonCurrentAssurance((int) $assurance->person_id);
        }

        return back()->with('success', 'تم تأمين السجلات المحددة بنجاح.');
    }

    public function printSelected(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:person_assurances,id',
        ]);

        $assurancesQuery = PersonAssurance::query()
            ->with(['person.user.complex', 'reservation'])
            ->whereIn('id', $request->ids)
            ->orderBy('id');

        $this->scopeAssuranceEloquent($assurancesQuery);

        $assurances = $assurancesQuery->get();

        foreach ($assurances as $assurance) {
            if ($assurance->status === 'pending') {
                $assurance->update([
                    'status' => 'assured',
                    'assured_at' => now(),
                    'printed_at' => now(),
                    'printed_by' => Auth::id(),
                ]);
            }

            $this->syncPersonCurrentAssurance((int) $assurance->person_id);
        }

        return view('admin.assurances.print', compact('assurances'));
    }

    private function syncPersonCurrentAssurance(int $personId): void
    {
        $today = now()->toDateString();
        $person = Person::find($personId);

        if (!$person) {
            return;
        }

        $active = PersonAssurance::where('person_id', $personId)
            ->whereIn('status', ['pending', 'assured'])
            ->whereDate('end_date', '>=', $today)
            ->orderByDesc('end_date')
            ->first();

        if ($active) {
            $person->update([
                'etat_ass' => $active->status === 'assured' ? 1 : 0,
                'assurance_status' => $active->status,
                'assurance_start_date' => $active->start_date,
                'assurance_end_date' => $active->end_date,
                'assured_by' => Auth::id(),
                'assured_at' => now(),
            ]);
            return;
        }

        $last = PersonAssurance::where('person_id', $personId)
            ->orderByDesc('end_date')
            ->first();

        if ($last) {
            $person->update([
                'etat_ass' => 0,
                'assurance_status' => $last->status,
                'assurance_start_date' => $last->start_date,
                'assurance_end_date' => $last->end_date,
                'assured_by' => Auth::id(),
                'assured_at' => now(),
            ]);
        }
    }

    private function hasPreviousAssurance(int $personId): bool
    {
        return PersonAssurance::where('person_id', $personId)->exists();
    }

    private function formatDate($date): string
    {
        return $date ? Carbon::parse($date)->format('Y/m/d') : '---';
    }

    private function formatDateTime($date): string
    {
        return $date ? Carbon::parse($date)->format('Y/m/d H:i') : '---';
    }

    private function statusBadge(?string $status): string
    {
        $label = match ($status) {
            'pending' => 'معني بالتأمين',
            'assured' => 'مؤمن',
            'expired' => 'منتهي',
            'cancelled' => 'ملغى',
            default => $status ?: '---',
        };

        return '<span class="badge-ass ' . e($status ?? 'unknown') . '">' . e($label) . '</span>';
    }

    private function operationLabel(?string $type): string
    {
        return match ($type) {
            'new' => 'تسجيل جديد',
            'renewal' => 'تجديد',
            default => $type ?: '---',
        };
    }
}
