<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Journal d'audit : qui a fait quoi, quand, depuis où.
 * Ne doit JAMAIS faire échouer l'opération métier : toute erreur est avalée.
 */
class AuditService
{
    /** Champs jamais enregistrés en clair */
    private const SECRET = ['password', 'password_confirmation', 'current_password', 'remember_token', '_token', '_method', 'token'];

    private static bool $busy = false;

    public static function record(string $event, array $data = [], $user = null): void
    {
        if (self::$busy) {
            return;
        }
        // On ne garde que les modifications / suppressions faites par les administrateurs
        // (pas les connexions, créations, ni les actions des personnes / entreprises / clubs).
        if (!in_array($event, ['updated', 'deleted', 'sql_delete'], true)) {
            return;
        }
        $u = $user ?: Auth::user();
        if (!$u || !in_array($u->type, ['admin', 'financier', 'verify_qr', 'verificateur'], true)) {
            return;
        }
        self::$busy = true;
        try {
            $user = $user ?: Auth::user();
            $req  = app()->runningInConsole() ? null : request();

            AuditLog::create(array_merge([
                'user_id'    => $user?->id,
                'user_name'  => $user?->name ?? $user?->email,
                'user_type'  => $user?->type,
                'event'      => $event,
                'route'      => $req?->route()?->getName(),
                'method'     => $req?->method(),
                'url'        => $req ? mb_substr($req->fullUrl(), 0, 1000) : (app()->runningInConsole() ? 'console' : null),
                'ip'         => $req?->ip(),
                'user_agent' => $req ? mb_substr((string) $req->userAgent(), 0, 250) : null,
            ], $data));
        } catch (\Throwable $e) {
            try { Log::warning('Audit: ' . $e->getMessage()); } catch (\Throwable $e2) {}
        } finally {
            self::$busy = false;
        }
    }

    public static function model(string $event, Model $model): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        $old = $new = null;
        if ($event === 'created') {
            $new = self::clean($model->getAttributes());
        } elseif ($event === 'updated') {
            $changes = $model->getChanges();
            unset($changes['updated_at']);
            if (!$changes) {
                return;
            }
            $new = self::clean($changes);
            $old = self::clean(array_intersect_key($model->getOriginal(), $changes));
        } elseif ($event === 'deleted') {
            // instantané complet : permet de récupérer la donnée supprimée
            $old = self::clean($model->getOriginal() ?: $model->getAttributes());
        }

        self::record($event, [
            'auditable_type' => get_class($model),
            'auditable_id'   => (string) $model->getKey(),
            'old_values'     => $old,
            'new_values'     => $new,
        ]);
    }

    public static function clean(array $values): array
    {
        foreach ($values as $k => $v) {
            if (in_array($k, self::SECRET, true)) {
                $values[$k] = '***';
            } elseif (is_string($v) && !mb_check_encoding($v, 'UTF-8')) {
                $values[$k] = '[binaire] ' . strlen($v) . ' o';
            } elseif (is_string($v) && mb_strlen($v) > 2000) {
                $values[$k] = mb_substr($v, 0, 2000) . '…';
            } elseif (is_array($v)) {
                $values[$k] = self::clean($v);
            } elseif (is_object($v) && !($v instanceof \JsonSerializable) && !method_exists($v, '__toString')) {
                $values[$k] = get_class($v);
            }
        }
        return $values;
    }
}
