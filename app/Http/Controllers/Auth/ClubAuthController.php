<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Club;
use App\Models\Complex;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class ClubAuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLogin()
    {
        return view('auth.club.login');
    }

    /**
     * عرض صفحة التسجيل مع قائمة المجمعات
     */
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

    return view('auth.club.register', compact(
        'complexes', 'selectedComplex', 'complexId', 'correctWord'
    ));
}

    /**
     * تسجيل نادي جديد
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users',
            'password'         => 'required|min:6|confirmed',
            'date_expiration'  => 'required|date',
            'numero_agrement'  => 'required|string|max:255',
            'complex_id'       => 'required|exists:complexes,id',
            'attachments'      => 'required',
            'attachments.*'    => 'mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
     
            'attachments.required' => 'يرجى إرفاق ملفات الاعتماد',
        ]);
if (trim(strtolower($request->captcha_word)) !== strtolower(session('captcha_word'))) {
    return back()->withErrors([
        'captcha_word' => "❌ التحقق غير صحيح، يرجى إعادة المحاولة."
    ])->withInput();
}

        DB::beginTransaction();
        $savedFiles = [];

        try {

            // إنشاء المستخدم
            $user = User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'type'       => 'club',
                'complex_id' => $request->complex_id,
            ]);

            // مسار التخزين الخاص بالصور والملفات
            if (app()->environment('local')) {
                $storagePath = storage_path('app/public/clubs');
                $storageUrl  = '/storage/clubs';
            } else {
                $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/') . '/clubs';
                $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/') . '/clubs';
            }

            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0777, true);
            }

            // حفظ الملفات
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move($storagePath, $fileName);
                $savedFiles[] = $storageUrl . '/' . $fileName;
            }

            // تسجيل النادي
            Club::create([
                'user_id'         => $user->id,
                'nom'             => $request->name,
                'numero_agrement' => $request->numero_agrement,
                'date_expiration' => $request->date_expiration,
                'complex_id'      => $request->complex_id,
                'attachments'     => json_encode($savedFiles, JSON_UNESCAPED_UNICODE),
                'entity_type'     => 'club',
            ]);

            DB::commit();

            Auth::login($user);

            return redirect()->route('club.dashboard')
                ->with('success', 'تم التسجيل بنجاح 🎉');

        } catch (\Exception $e) {

            // إزالة الملفات التي تم رفعها
            foreach ($savedFiles as $file) {
                $localPath = str_replace('/storage', 'storage/app/public', $file);
                if (file_exists($localPath)) {
                    @unlink($localPath);
                }
            }

            DB::rollBack();

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء التسجيل. يرجى المحاولة لاحقاً.'])
                ->withInput();
        }
    }

    /**
     * تسجيل الدخول للنادي
     */
    public function login(Request $request)
    {
        // قواعد التحقق
        $rules = [
            'email'    => 'required|email',
            'password' => 'required'
        ];

        if (!app()->environment('local')) {
            $rules['g-recaptcha-response'] = 'required';
        }

        $request->validate($rules);

        // تحقق reCAPTCHA
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
                return back()->withErrors([
                    'g-recaptcha-response' => 'رمز CAPTCHA غير صالح'
                ]);
            }
        }

        // محاولة تسجيل الدخول
        if (Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password,
            'type'     => 'club',
        ])) {
            $request->session()->regenerate();
            return redirect()->route('club.dashboard');
        }

        return back()->withErrors([
            'email' => '❌ معلومات الدخول غير صحيحة أو الحساب ليس ناديًا'
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('club.login');
    }
}

