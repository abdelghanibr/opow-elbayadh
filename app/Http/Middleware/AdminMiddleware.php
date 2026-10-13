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

        // يسمح للـ admin / financier / verify_qr
        if (!in_array($user->type, ['admin', 'financier', 'verify_qr'])) {
            abort(403, 'غير مصرح لك');
        }

        $routeName = (string) $request->route()?->getName();

        // ================================
        // 💰 المالي → فقط الوضعية المالية (الحجوزات / المبالغ حسب المركب)
        // ================================
        if ($user->type === 'financier') {
            $allowedFinanceRoutes = [
                'reservations.index',
                'reservations.togglePayment',
                'reservations.print',
                'admin.profile.edit',
                'admin.profile.update',
            ];

            if (in_array($routeName, $allowedFinanceRoutes)) {
                return $next($request);
            }

            return redirect()->route('reservations.index');
        }

        // ================================
        // 🎫 مدقق QR → فقط صفحة مسح التذاكر
        // ================================
        if ($user->type === 'verify_qr') {
            $allowedVerifyRoutes = [
                'tickets.verify-qr',
                'ticket.check-qr',
                'admin.profile.edit',
                'admin.profile.update',
            ];

            if (in_array($routeName, $allowedVerifyRoutes)) {
                return $next($request);
            }

            return redirect()->route('tickets.verify-qr');
        }

        // ================================
        // 👑 مسؤول كامل → الجميع، مع منطق المجمع
        // ================================

        // إذا طلب صفحة admin.dashboard
        if ($routeName === 'admin.dashboard') {

            // إذا كان لديه complex_id → منعه من رؤية لوحة التحكم العامة
            if (!is_null($user->complex_id) && $user->complex_id != 0) {
                return redirect()->route('admin.dashboard_complex', $user->complex_id);
            }
        }

        // إذا طلب صفحة complex dashboard
        if ($routeName === 'admin.dashboard_complex') {

            $requestedId = $request->route('id');

            // منع الدخول إلى مجمع غير مربوط به
            if ($user->complex_id != $requestedId) {
                abort(403, 'غير مصرح لك بدخول هذا المجمع');
            }
        }

        return $next($request);
    }
}
