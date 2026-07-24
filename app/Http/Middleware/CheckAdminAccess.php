<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdminAccess
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // 🔒 إذا لم يكن مستخدمًا
        if (!$user) {
            return redirect()->route('login');
        }

        // 🔒 إذا كان admin كامل يمكنه الدخول للجميع
        if ($user->type === 'admin' && ($user->complex_id == null || $user->complex_id == 0)) {
            return $next($request);
        }

        // 🔥 إذا حاول دخول dashboard العام وهو Admin Complex فقط
        if ($user->type === 'admin' && $user->complex_id != null && $user->complex_id != 0) {
            return redirect()->route('admin.dashboard_complex', $user->complex_id)
                ->with('error', '⛔ غير مسموح لك بدخول لوحة التحكم العامة');
        }

        // 🚫 منع أي نوع مستخدم آخر
        return abort(403, 'غير مصرح لك بالوصول');
    }
}
