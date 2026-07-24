<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

        if ($request->is('admin/*')) {
            return route('admin.login');
        }

        if ($request->is('club/*')) {
            return route('club.login');
        }

        if ($request->is('entreprise/*')) {
            return route('entreprise.login');
        }

        if ($request->is('person/*')) {
            return route('person.login');
        }

        // ⛔ لا redirect افتراضي
        return route('welcome');
    }
        
        
        
        
        
    }
}

