<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;

/**
 * Journalise chaque action d'écriture (POST/PUT/PATCH/DELETE) d'un utilisateur connecté :
 * qui, quelle route, quelles données envoyées (mots de passe masqués).
 */
class AuditRequests
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
                && auth()->check()
                && !$request->routeIs('login*', 'admin.login*', 'logout', 'admin.logout')) {
                $input = $request->except(['password', 'password_confirmation', 'current_password', '_token', '_method']);
                foreach ($request->allFiles() as $k => $f) {
                    $input[$k] = is_array($f) ? '[fichiers]' : '[fichier] ' . $f->getClientOriginalName();
                }
                AuditService::record('request', [
                    'new_values' => ['status' => $response->getStatusCode(), 'input' => AuditService::clean($input)],
                ]);
            }
        } catch (\Throwable $e) {
            // ne jamais casser la requête
        }

        return $response;
    }
}
