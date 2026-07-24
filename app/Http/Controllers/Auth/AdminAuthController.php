<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.admin.login');
    }

    public function login(Request $request)
    {
        // قواعد التحقق
        $rules = [
            'email'    => 'required|email',
            'password' => 'required',
        ];
/*
        // CAPTCHA خارج البيئة المحلية فقط
        if (!app()->environment('local')) {
            $rules['g-recaptcha-response'] = 'required';
        }

        // تنفيذ الفاليديشن
        $request->validate($rules, [
            'g-recaptcha-response.required' => '⚠️ يرجى تأكيد أنك لست روبوتًا',
        ]);

        // التحقق من Google reCAPTCHA (خارج local فقط)
        if (!app()->environment('local')) {

            $response = Http::asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret'   => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $request->input('g-recaptcha-response'),
                    'remoteip' => $request->ip(),
                ]
            );

            if (!($response->json()['success'] ?? false)) {
                return back()
                    ->withErrors(['g-recaptcha-response' => '❌ CAPTCHA غير صالحة'])
                    ->withInput();
            }
        }
*/
        // محاولة تسجيل الدخول
        if (Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ])) {

            $user = Auth::user();

            // التأكد من أن المستخدم Admin
            if ($user->type !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => '⛔ هذا الحساب غير مصرح له بالدخول إلى لوحة الإدارة',
                ]);
            }

            // إعادة توليد الجلسة
            $request->session()->regenerate();

            // ================================
            // 💡 التحقق من complex_id
            // ================================
          if (empty($user->complex_id)) {
    // لا يوجد complex → الذهاب للوحة العامة
    return redirect()->route('admin.dashboard');
} else {
    // عنده complex → الذهاب للوحة المجمع
    return redirect()->route('admin.dashboard_complex', $user->complex_id);
}

        }

        // بيانات خاطئة
        return back()->withErrors([
            'email' => '❌ البريد الإلكتروني أو كلمة المرور غير صحيحة',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
