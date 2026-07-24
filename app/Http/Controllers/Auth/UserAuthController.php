<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Person;
use App\Models\Club;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Login process
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt(
            ['email' => $request->email, 'password' => $request->password],
            $remember
        )) {
            $user = Auth::user();

            // Redirect based on role
            return match ($user->type) {
                'admin' => redirect()->route('admin.dashboard'),
                'club'  => redirect()->route('club.dashboard'),
                'company' => redirect()->route('entreprise.dashboard'),
                default => redirect()->route('person.dashboard'),
            };
        }

        return back()->withErrors(['email' => '⚠️ معلومات تسجيل الدخول غير صحيحة!']);
    }

    /**
     * Show register form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Register process
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:120',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:50',
            'password' => 'required|min:6|confirmed',
            'type'     => 'required|in:person,club,entreprise'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'type'     => $request->type
        ]);

        // Create related model when needed
        if ($user->type == 'person') {
            Person::create(['user_id' => $user->id]);
        }
        if ($user->type == 'club') {
            Club::create(['user_id' => $user->id]);
        }

        Auth::login($user);

        return redirect()->route('person.dashboard')
                         ->with('success', 'تم إنشاء الحساب بنجاح');
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
