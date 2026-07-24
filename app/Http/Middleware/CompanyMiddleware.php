<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CompanyMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->type === 'company') {
            return $next($request);
        }

        abort(403);
    }
}
