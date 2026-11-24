<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ImpersonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImpersonationController extends Controller
{
    /** Liste (JSON, à la demande) des comptes vers lesquels on peut basculer. */
    public function users(Request $request)
    {
        $actor = Auth::user();
        abort_unless(ImpersonationService::isSuperAdmin($actor) && !ImpersonationService::isImpersonating(), 403);

        $q    = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', '');

        $query = User::query()
            ->leftJoin('complexes', 'complexes.id', '=', 'users.complex_id')
            ->where('users.id', '!=', $actor->id)
            // pas d'autre super admin
            ->where(function ($w) {
                $w->where('users.type', '!=', 'admin')
                  ->orWhere(function ($a) {
                      $a->whereNotNull('users.complex_id')->where('users.complex_id', '!=', 0);
                  });
            })
            ->select('users.id', 'users.name', 'users.email', 'users.type', 'users.complex_id', 'complexes.nom as complex_name')
            ->orderBy('users.name')
            ->limit(20);

        if ($type !== '') {
            $query->where('users.type', $type);
        }

        if ($q !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('users.name', 'like', $like)
                  ->orWhere('users.email', 'like', $like)
                  ->orWhere('users.type', 'like', $like)
                  ->orWhere('complexes.nom', 'like', $like);
            });
        } else {
            // sans recherche : uniquement les admins de complexe (liste courte)
            $query->where('users.type', '!=', 'person');
        }

        return response()->json($query->get());
    }

    public function start(Request $request, int $user)
    {
        $actor  = Auth::user();
        $target = User::find($user);

        if ($message = ImpersonationService::denial($actor, $target)) {
            abort(403, $message);
        }

        ImpersonationService::start($request, $actor, $target);

        return redirect(ImpersonationService::homeUrl($target))
            ->with('success', 'أنت متصل الآن بحساب : ' .($target->name ?? $target->email));
    }

    public function stop(Request $request)
    {
        if (!ImpersonationService::isImpersonating()) {
            abort(403, 'لا يوجد تبديل حساب جارٍ.');
        }

        if (!ImpersonationService::stop($request)) {
            return redirect()->route('admin.login');
        }

        return redirect()->route('admin.dashboard')->with('success', 'تمت العودة إلى حسابك.');
    }
}
