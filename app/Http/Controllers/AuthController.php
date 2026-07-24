<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

   public function login(Request $request)
{
    // التحقق من المدخلات
    $request->validate([
        'login' => 'required|string',
        'password' => 'required|string',
    ]);

    // نحدد إذا كان ما أدخله المستخدم هو بريد أو اسم مستخدم
    $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
        ? 'email'
        : 'username';

    // نبحث هل المستخدم موجود أصلاً في قاعدة البيانات
    $user = \App\Models\User::where($loginField, $request->login)->first();

    // إن لم نجد المستخدم → الخطأ من البريد/اسم المستخدم
    if (!$user) {
        return back()->withErrors([
            'login' => '❌ البريد الإلكتروني أو اسم المستخدم غير صحيح.',
        ])->withInput();
    }

    // إن وجد المستخدم ولكن كلمة المرور خطأ
    if (!\Hash::check($request->password, $user->password)) {
        return back()->withErrors([
            'password' => '❌ كلمة المرور غير صحيحة.',
        ])->withInput();
    }

    // محاولة تسجيل الدخول
    if (Auth::attempt([$loginField => $request->login, 'password' => $request->password], $request->filled('remember'))) {
        return redirect()->route('dashboard');
    }

    // احتياط (لن نصل لها عادة)
    return back()->withErrors([
        'login' => '❌ خطأ غير متوقع أثناء تسجيل الدخول.',
    ]);
}

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
