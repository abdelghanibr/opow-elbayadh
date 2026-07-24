<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

   
public function login(Request $request)
{
    $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];

    if (!app()->environment('local')) {
        $rules['g-recaptcha-response'] = 'required';
    }

  /*  $request->validate($rules, [
        'g-recaptcha-response.required' => '⚠️ يرجى تأكيد أنك لست روبوتًا',
    ]);

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
                ->withErrors(['g-recaptcha-response' => '❌ فشل التحقق من reCAPTCHA'])
                ->withInput();
        }
    }
*/
    if (Auth::attempt(
        ['email' => $request->email, 'password' => $request->password],
        $request->filled('remember')
    )) {
        return redirect()->route('admin.dashboard');
    }

    return back()->withErrors([
        'email' => '❌ البريد الإلكتروني أو كلمة المرور غير صحيحة',
    ]);
}


    /**
     * تسجيل الخروج
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
