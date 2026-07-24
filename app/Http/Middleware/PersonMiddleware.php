<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PersonMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->type === 'person') {
            return $next($request);
        }

        abort(403, 'Accès refusé');
    }
}
