<?php

namespace App\Providers;

use App\Services\AuditService;
use App\Services\BackupService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('fr');
        Carbon::macro('fr', function () {
            return $this->format('d/m/Y');
        });
        Carbon::macro('frLong', function () {
            return $this->isoFormat('dddd D MMMM YYYY'); // vendredi 19 décembre 2025
        });

        $this->bootAudit();
    }

    /**
     * Traçabilité des utilisateurs + sauvegarde à chaque connexion admin.
     */
    private function bootAudit(): void
    {
        // --- 1. Toute création / modification / suppression de modèle Eloquent ---
        foreach (['created', 'updated', 'deleted'] as $ev) {
            Event::listen("eloquent.{$ev}: *", function ($eventName, array $payload) use ($ev) {
                if (isset($payload[0]) && $payload[0] instanceof \Illuminate\Database\Eloquent\Model) {
                    AuditService::model($ev, $payload[0]);
                }
            });
        }

        // --- 2. Connexions / déconnexions / échecs ---
        Event::listen(Login::class, function (Login $e) {
            AuditService::record('login', [], $e->user);

            // Sauvegarde de la base à chaque connexion d'un administrateur
            // (uniquement lors d'une vraie saisie du formulaire, pas d'un cookie "remember").
            if (($e->user->type ?? null) === 'admin'
                && !app()->runningInConsole()
                && request()->isMethod('post')) {
                app()->terminating(function () {
                    BackupService::run('login_admin');
                });
            }
        });
        Event::listen(Logout::class, function (Logout $e) {
            if ($e->user) {
                AuditService::record('logout', [], $e->user);
            }
        });
        Event::listen(Failed::class, function (Failed $e) {
            $id = $e->credentials['email'] ?? $e->credentials['username'] ?? $e->credentials['login'] ?? null;
            AuditService::record('login_failed', ['new_values' => ['identifiant' => $id]], $e->user);
        });

        // --- 3. Suppressions "de masse" (query builder) qui n'émettent pas d'événement Eloquent ---
        if (!app()->runningInConsole()) {
            DB::listen(function ($q) {
                if (preg_match('/^\s*(delete|truncate|drop)\b/i', $q->sql)
                    && !preg_match('/`?(audit_logs|sessions|cache|cache_locks|jobs)`?/i', $q->sql)) {
                    AuditService::record('sql_delete', [
                        'new_values' => ['sql' => mb_substr($q->sql, 0, 1000), 'bindings' => AuditService::clean(array_map('strval', array_map(fn ($b) => is_scalar($b) || $b === null ? $b : '?', $q->bindings)))],
                    ]);
                }
            });
        }
    }
}
