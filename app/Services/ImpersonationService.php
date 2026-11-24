<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Bascule du super admin vers un autre compte (et retour).
 * Un seul guard « web » / une seule session : on remplace l'utilisateur de la session et on garde
 * l'identité d'origine dans `impersonator_id`. L'événement Login n'est pas déclenché
 * (pas de sauvegarde de base, pas de journal de connexion).
 */
class ImpersonationService
{
    public const SESSION_KEY = 'impersonator_id';

    /** Super admin = admin de tous les complexes (même règle que CheckAdminAccess). */
    public static function isSuperAdmin(?User $user): bool
    {
        return $user && $user->type === 'admin' && (is_null($user->complex_id) || (int) $user->complex_id === 0);
    }

    public static function isImpersonating(): bool
    {
        return (bool) session(self::SESSION_KEY);
    }

    /** Peut-on entrer dans $target depuis la session courante ? Retourne un message d'erreur ou null. */
    public static function denial(?User $actor, ?User $target): ?string
    {
        if (self::isImpersonating()) {
            return 'أنت متصل حاليًا عبر تبديل حساب : عد أولًا إلى حسابك.';
        }
        if (!self::isSuperAdmin($actor)) {
            return 'هذه الميزة مخصصة للمشرف العام فقط.';
        }
        if (!$target) {
            return 'الحساب غير موجود.';
        }
        if ($target->id === $actor->id) {
            return 'لا يمكن التبديل إلى حسابك الخاص.';
        }
        if (self::isSuperAdmin($target)) {
            return 'لا يمكن التبديل إلى حساب مشرف عام آخر.';
        }

        return null;
    }

    public static function start(Request $request, User $actor, User $target): void
    {
        self::switchTo($target);
        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, $actor->id);

        self::log('impersonate_start', $actor, $target, $request);
    }

    /** Retour au compte d'origine. Retourne false si la session n'a pas été ouverte par une bascule. */
    public static function stop(Request $request): bool
    {
        $originId = $request->session()->get(self::SESSION_KEY);
        $current  = Auth::user();
        $origin   = $originId ? User::find($originId) : null;

        if (!$originId) {
            return false;
        }

        if (!self::isSuperAdmin($origin)) {
            // le compte d'origine n'existe plus / n'est plus super admin : on ferme la session
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return false;
        }

        self::switchTo($origin);
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->regenerate();

        self::log('impersonate_stop', $origin, $current, $request);

        return true;
    }

    /** Page d'accueil adaptée au type du compte. */
    public static function homeUrl(User $user): string
    {
        return match ($user->type) {
            'admin'                    => route('admin.dashboard'), // un admin de complexe est redirigé vers son complexe
            'verificateur', 'financier' => route('reservations.index'),
            'club'                     => route('club.dashboard'),
            'company', 'entreprise'    => route('entreprise.dashboard'),
            default                    => route('person.dashboard'),
        };
    }

    /**
     * Remplace l'utilisateur de la session SANS déclencher l'événement Login
     * (aucun listener de connexion : pas de sauvegarde, pas de journal de connexion, pas de « dernière connexion »).
     */
    private static function switchTo(User $user): void
    {
        $guard = Auth::guard();
        session()->put($guard->getName(), $user->getAuthIdentifier());
        $guard->setUser($user);
    }

    /**
     * Journal : table audit_logs si elle existe (projet avec journal d'audit), sinon fichier de log Laravel.
     * Aucune dépendance au modèle AuditLog / AuditService.
     */
    private static function log(string $event, User $actor, ?User $target, Request $request): void
    {
        $values = [
            'from' => ['id' => $actor->id, 'email' => $actor->email, 'type' => $actor->type],
            'to'   => $target ? ['id' => $target->id, 'email' => $target->email, 'type' => $target->type, 'name' => $target->name] : null,
        ];

        try {
            if (Schema::hasTable('audit_logs')) {
                DB::table('audit_logs')->insert([
                    'user_id'    => $actor->id,
                    'user_name'  => $actor->name ?? $actor->email,
                    'user_type'  => $actor->type,
                    'event'      => $event,
                    'route'      => $request->route()?->getName(),
                    'method'     => $request->method(),
                    'url'        => mb_substr($request->fullUrl(), 0, 1000),
                    'ip'         => $request->ip(),
                    'user_agent' => mb_substr((string) $request->userAgent(), 0, 250),
                    'new_values' => json_encode($values, JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                ]);

                return;
            }

            Log::info($event, ['ip' => $request->ip()] + $values);
        } catch (\Throwable $e) {
            try {
                Log::warning('Impersonation log: ' . $e->getMessage());
            } catch (\Throwable $e2) {
            }
        }
    }
}
