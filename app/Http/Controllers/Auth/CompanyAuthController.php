<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Club;
use App\Models\Complex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CompanyAuthController extends Controller
{
    /**
     * شاشة تسجيل الدخول
     */
    public function showLogin()
    {
        return view('auth.company.login');
    }

    /**
     * شاشة التسجيل — مع قائمة المجمعات
     */
public function showRegister(Request $request)
{
    $complexId = $request->get('complex');
    $selectedComplex = $complexId ? Complex::find($complexId) : null;

    $complexes = Complex::orderBy('nom')->get();

    return view('auth.company.register', compact(
        'complexes', 'selectedComplex', 'complexId'
    ));
}

    /**
     * تسجيل مؤسسة جديدة
     */
    public function register(Request $request)
    {
        $request->merge([
            'phone' => preg_replace('/[\s.\-()]/', '', (string) $request->phone),
        ]);

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users',
            'phone'        => ['required', 'string', 'regex:/^0[0-9]{8,9}$/'],
            'password'     => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^a-zA-Z0-9]/'],
            'complex_id'   => 'required|exists:complexes,id',
            'company_type' => 'required|in:public,privee',
            'attachments'  => 'nullable|array',
            'attachments.*'=> 'mimes:pdf,jpg,jpeg,png|max:4096'
        ], [
            'complex_id.required' => 'يرجى اختيار المجمع الرياضي التابع للمؤسسة.',
            'phone.required'      => 'رقم هاتف المسؤول مطلوب.',
            'phone.regex'         => 'أدخل رقم هاتف جزائري صحيح (مثال: 0550123456).',
            'company_type.required' => 'يرجى اختيار نوع المؤسسة.',
            'company_type.in'     => 'نوع المؤسسة غير صحيح.',
            'password.min'        => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.regex'      => 'كلمة المرور يجب أن تحتوي على حرف كبير وحرف صغير ورقم ورمز خاص.',
            'password.confirmed'  => 'كلمتا المرور غير متطابقتين.',
        ]);
if (!app()->environment('local')) {
    $request->validate([
        'g-recaptcha-response' => 'required',
    ], [
        'g-recaptcha-response.required' => '⚠️ يرجى تأكيد أنك لست روبوتًا',
    ]);

    $captcha = Http::asForm()->post(
        'https://www.google.com/recaptcha/api/siteverify',
        [
            'secret'   => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]
    );

    if (!($captcha->json()['success'] ?? false)) {
        return back()
            ->withErrors(['g-recaptcha-response' => '❌ رمز CAPTCHA غير صالح'])
            ->withInput();
    }
}
        DB::beginTransaction();
        $savedFiles = [];

        try {
            // 1️⃣ إنشاء المستخدم
            $user = User::create([
                'name'        => $request->name,
                'email'       => $request->email,
                'phone'       => $request->phone,
                'password'    => Hash::make($request->password),
                'type'        => 'company',
                'complex_id'  => $request->complex_id,
            ]);

            // 2️⃣ إعداد مسار التخزين
            if (app()->environment('local')) {
                $storagePath = storage_path('app/public/companies');
                $storageUrl  = '/storage/companies';
            } else {
                $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/') . '/companies';
                $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/') . '/companies';
            }

            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0777, true);
            }

            // 3️⃣ رفع الملفات (اختياري)
            foreach ((array) $request->file('attachments') as $file) {
                if (!$file) {
                    continue;
                }
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move($storagePath, $fileName);
                $savedFiles[] = $storageUrl . '/' . $fileName;
            }

            // 4️⃣ إنشاء سجل في جدول clubs للكيان company
            Club::create([
                'user_id'         => $user->id,
                'nom'             => $request->name,
                'entity_type'     => 'company',
                'company_type'    => $request->company_type,
                'complex_id'      => $request->complex_id,
                'attachments'     => $savedFiles ? json_encode($savedFiles, JSON_UNESCAPED_UNICODE) : null,
            ]);

            DB::commit();

            // 5️⃣ تسجيل الدخول مباشرة
            Auth::login($user);

            return redirect()->route('entreprise.dashboard')
                ->with('success', 'تم تسجيل المؤسسة بنجاح 🎉');

        } catch (\Exception $e) {

            DB::rollBack();

            // حذف الملفات المحملة
            foreach ($savedFiles as $file) {
                $localFile = str_replace('/storage', 'storage/app/public', $file);
                if (file_exists($localFile)) {
                    @unlink($localFile);
                }
            }

            return back()
                ->withErrors(['error' => '⚠️ حدث خطأ أثناء التسجيل. يرجى المحاولة لاحقاً.'])
                ->withInput();
        }
    }

    /**
     * تسجيل الدخول للمؤسسات
     */
    public function login(Request $request)
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required',
        ];

        if (!app()->environment('local')) {
            $rules['g-recaptcha-response'] = 'required';
        }

        $request->validate($rules);

        // تحقق CAPTCHA خارج Local فقط
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
                    'g-recaptcha-response' => '❌ رمز التحقق غير صحيح'
                ]);
            }
        }

        // محاولة تسجيل الدخول
        if (Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password,
            'type'     => 'company',
        ])) {
            $request->session()->regenerate();
            return redirect()->route('entreprise.dashboard');
        }

        return back()->withErrors([
            'email' => '❌ معلومات الدخول غير صحيحة أو الحساب ليس مؤسسة',
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('entreprise.login');
    }
}

