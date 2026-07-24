<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Complex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // ========================
    // 👇 إظهار فورم التسجيل
    // ========================
    public function showRegistrationForm()
    {
        $complexes = Complex::all(); // تحميل قائمة المجمعات
        return view('auth.register', compact('complexes'));
    }

    // ========================
    // 👇 فورم تعديل الحساب
    // ========================
    public function edit()
    {
        $user = Auth::user();
        $complexes = Complex::all();

        if (!$user) {
            return redirect()->route('login');
        }

        switch ($user->type) {
            case 'admin':
                return view('admin.profile.edit', compact('user','complexes'));

            case 'club':
                return view('club.profile.edit',compact('user','complexes'));

            case 'person':
                return view('profile.edit', compact('user','complexes'));

            case 'company':
                return view('entreprise.profile.edit', compact('user','complexes'));

            default:
                abort(403, 'Unauthorized access');
        }
    }

    // ========================
    // 👇 تحديث بيانات الحساب
    // ========================
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
           
            'complex_id' => 'required|exists:complexes,id', // 🔥 تم إضافته
            'password' => 'nullable|confirmed|min:8',
        ]);

        // تحديث الحقول العامة
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->complex_id = $request->complex_id; // ✔️

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // إعادة التوجيه حسب نوع المستخدم
        switch ($user->type) {
            case 'person':
                return redirect()->route('person.profile.edit')
                    ->with('success', 'تم تحديث معلوماتك بنجاح 🎯');

            case 'club':
                return redirect()->route('club.profile.edit')
                    ->with('success', 'تم تحديث بيانات النادي 👍');

            case 'company':
            case 'entreprise':
                return redirect()->route('entreprise.profile.edit')
                    ->with('success', '✔ تم تحديث حساب الشركة!');

            default:
                abort(403, 'غير مصرح لك بتنفيذ هذا الإجراء');
        }
    }

    // ========================
    // 👇 تسجيل مستخدم جديد
    // ========================
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname'  => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:255', 'unique:users'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],

            'complex_id' => ['required', 'exists:complexes,id'], // 🔥 جديد

            'password'  => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ],
        [
            'email.unique' => 'هذا البريد الإلكتروني مسجّل مسبقاً.',
            'username.unique' => 'اسم المستخدم مسجّل مسبقاً.',
            'complex_id.required' => 'يرجى اختيار المجمع الرياضي.',
            'complex_id.exists'   => 'المجمع المختار غير موجود.',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين.',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وصغير ورقم ورمز خاص.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create user
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'username'  => $request->username,
            'email'     => $request->email,
            'name'      => $request->firstname . ' ' . $request->lastname,
            'password'  => Hash::make($request->password),
            'complex_id' => $request->complex_id, // ✔️ إضافة المجمع
        ]);

        return redirect()->route('register.success')
                         ->with('name', $user->name);
    }
}
