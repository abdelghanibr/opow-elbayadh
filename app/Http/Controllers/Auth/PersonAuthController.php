<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Person;
use App\Models\Complex;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class PersonAuthController extends Controller
{
    /**
     * ⬅️ شاشة تسجيل الدخول
     */
    public function showLogin()
    {
        return view('auth.person.login');
    }

    /**
     * ⬅️ شاشة التسجيل — مع قائمة المجمعات
     */
  /*  public function showRegister()
    {
        $complexes = Complex::orderBy('nom')->get();

        return view('auth.person.register', compact('complexes'));
    }*/
public function showRegister(Request $request)
{
    $complexId = $request->get('complex');
    $selectedComplex = $complexId ? Complex::find($complexId) : null;

    $complexes = Complex::orderBy('nom')->get();
    
     $activities = [];
    if ($complexId) {
        $activities = Activity::whereHas('complexActivities', function ($q) use ($complexId) {
            $q->where('complex_id', $complexId);
        })->orderBy('title')->get();
    }


    // كلمات Captcha بالفرنسية
    $words = ["ordinateur", "sport", "complexe", "terrain", "piscine", "gymnase"];
    shuffle($words);

    $correctWord = $words[0]; // نختار كلمة واحدة فقط

    // حفظ الكلمة في الجلسة
    session(['captcha_word' => $correctWord]);

    return view('auth.person.register', compact(
        'complexes', 'selectedComplex', 'complexId', 'correctWord','activities'
    ));
}



    /**
     * ⬅️ معالجة التسجيل
     */
    public function register(Request $request)
    {
       //dd($request);
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|confirmed|min:8',
            'complex_id'  => 'required|exists:complexes,id',
            'birth_date'  => 'required|date|before:-3 years',
            'blood_type'  => 'nullable|string|max:5',
            'profession'  => 'nullable|string|max:100',
              'nin' => [
        'required',
        'digits:18',   // يجب أن يحتوي على 20 رقمًا بالضبط
   
    ],
            'privacy_policy' => 'accepted',
        ], [
       
            'privacy_policy.accepted' => 'يجب الموافقة على سياسة حماية البيانات',
            'birth_date.required'     => '⚠ تاريخ الميلاد مطلوب.',
            'birth_date.before'       => '⚠ يجب أن يكون العمر 3 سنوات على الأقل.',
        ]);
if (trim(strtolower($request->captcha_word)) !== strtolower(session('captcha_word'))) {
    return back()->withErrors([
        'captcha_word' => "❌ التحقق غير صحيح، يرجى إعادة المحاولة."
    ])->withInput();
}
$messages = [
    'nin.required' => '❌ رقم التعريف الوطني مطلوب.',
    'nin.digits'   => '❌ رقم التعريف الوطني يجب أن يحتوي على 20 رقمًا بالضبط.',
  
];

$request->validate([
    'nin' => ['required', 'digits:18'],
], $messages);


        // إنشاء المستخدم
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'nin' => $request->nin,
            'password'   => Hash::make($request->password),
            'type'       => 'person',
            'complex_id' => $request->complex_id,
        ]);

        // حساب فئة العمر
        $age = \Carbon\Carbon::parse($request->birth_date)->age;
        $ageCat = $age <= 12 ? 1 : ($age <= 17 ? 2 : ($age <= 100 ? 3 : 4));

        // إنشاء سجل Person للشخص
        Person::create([
            'user_id'    => $user->id,
            'complex_id' => $request->complex_id,
            'birth_date' => $request->birth_date,
            'blood_type' => $request->blood_type,
            'profession' => $request->profession,
            'age_category_id' => $ageCat,
        ]);

        // تسجيل دخول يدوي
        Auth::login($user);

        return redirect()->route('person.dashboard')
            ->with('success', 'تم إنشاء الحساب بنجاح 🎉');
    }

    /**
     * ⬅️ تسجيل الدخول
     */
    public function login(Request $request)
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required',
        ];

        $messages = [];

   /*     if (!app()->environment('local')) {
            $rules['g-recaptcha-response'] = 'required';
            $messages['g-recaptcha-response.required'] = '⚠️ xيرجى تأكيد أنك لست روبوتًا';
       }

        $request->validate($rules, $messages);

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
                    ->withErrors(['g-recaptcha-response' => '❌ رمز CAPTCHA غير صالح'])
                    ->withInput();
            }
        }
*/
        if (Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password,
            'type'     => 'person',
        ])) {
            $request->session()->regenerate();
            return redirect()->route('person.dashboard');
        }

        return back()->withErrors([
            'email' => '❌ بيانات الدخول غير صحيحة أو الحساب ليس فردًا',
        ]);
    }

    /**
     * ⬅️ تسجيل الخروج
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('person.login');
    }
}
