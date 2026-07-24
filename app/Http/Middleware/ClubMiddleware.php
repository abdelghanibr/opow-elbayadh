<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ClubMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->type === 'club') {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
