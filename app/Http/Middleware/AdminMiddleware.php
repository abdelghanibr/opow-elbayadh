<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
   /* public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->type === 'admin') {
            return $next($request);
        }

        abort(403);
    }*/
    
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // يسمح فقط للـ admin
        if ($user->type !== 'admin') {
            abort(403, 'غير مصرح لك');
        }

        // إذا طلب صفحة admin.dashboard
        if ($request->route()->getName() === 'admin.dashboard') {

            // إذا كان لديه complex_id → منعه من رؤية لوحة التحكم العامة
            if (!is_null($user->complex_id) && $user->complex_id != 0) {
                return redirect()->route('admin.dashboard_complex', $user->complex_id);
            }
        }

        // إذا طلب صفحة complex dashboard
        if ($request->route()->getName() === 'admin.dashboard_complex') {

            $requestedId = $request->route('id');

            // منع الدخول إلى مجمع غير مربوط به
            if ($user->complex_id != $requestedId) {
                abort(403, 'غير مصرح لك بدخول هذا المجمع');
            }
        }

        return $next($request);
    }
}
