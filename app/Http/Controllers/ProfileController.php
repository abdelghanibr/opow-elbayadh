<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Person;
use App\Models\Dossier;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index()
    {
        return redirect()->route('profile.step', 1);
    }

public function showStep($step)
{
    $user = Auth::user();
     $person = Person::where('user_id', $user->id)->orderByDesc('id')->firstOrFail();
     $dossier = Dossier::where('person_id', $person->id)->first();
   //dd($dossier->attachments);
    // 🔹 إذا كا مستخدم club أو entreprise
    if ($user->type === 'club' || $user->type === 'company') {

        if(session()->has('edit_person_id')) {
            $person = Person::find(session('edit_person_id'));
        } else {
            // 👈 إنشاء شخص جديد (حقول فارغة)
            $person = new Person();
        }

    } 
    // 🔹 إذا كان المستخدم فردي person
    else {
        $person = Person::where('user_id', $user->id)
                        ->orderByDesc('id')
                        ->first();
    }

    $wilayas = [
        "أدرار","الشلف","الأغواط","أم البواقي","باتنة","بجاية","بسكرة","بشار",
        "البليدة","البويرة","تمنراست","تبسة","تلمسان","تيارت","تيزي وزو","الجزائر",
        "الجلفة","جيجل","سطيف","سعيدة","سكيكدة","سيدي بلعباس","عنابة","قالمة",
        "قسنطينة","المدية","مستغانم","المسيلة","معسكر","ورقلة","وهران","البيض",
        "إليزي","برج بوعريريج","بومرداس","الطارف","تندوف","تيسمسيلت","الوادي",
        "خنشلة","سوق أهراس","تيبازة","البيض","عين الدفلى","النعامة","عين تموشنت",
        "غرداية","غليزان"
    ];

    return view('profile.steps', compact('step','user','person','wilayas','dossier'));
}




    public function saveStep(Request $request, $step)
    {
        $user = Auth::user();
        $type = $user->type;

         //$dossier = Dossier::where('person_id', $person->id)->first();
$person = Person::where('user_id', $user->id)->orderByDesc('id')->firstOrFail();
    $dossier = Dossier::where('person_id', $person->id)->first();
    
        switch ($step) {

            case 1:
                $validated = $request->validate([
                    'firstname' => 'required|string|max:50',
                    'lastname' => 'required|string|max:50',
                  'birth_date' => 'required|date|before:-3 years',
                    'gender' => 'required',
                    'blood_type' => 'nullable|string|max:5',
                    'profession' => 'nullable|string|max:100',
                    'handicap' => 'required' ,
                    'tuteur_fullname' =>  'required|string|max:50',
                ]);

                $age = Carbon::parse($request->birth_date)->age;
                $ageCat = $age <= 12 ? 1 : ($age <= 17 ? 2 : ($age <= 100 ? 3 : 4));

                if ($type === 'club' || $type === 'company') {
                    Person::create(array_merge($validated, [
                        'user_id' => $user->id,
                        'age_category_id' => $ageCat
                    ]));
                } else {
                    Person::updateOrCreate(
                        ['user_id' => $user->id],
                        array_merge($validated, ['age_category_id' => $ageCat])
                    );
                }

                return redirect()->route('profile.step', ($age < 18 ? 2 : 3));



            case 2:
                if ($type !== 'person') return redirect()->route('profile.step', 3);

                $validated = $request->validate([
                    'parent_firstname' => 'required|string|max:50',
                    'parent_lastname' => 'required|string|max:50',
                    'parent_phone' => 'required|string|max:20',
                ]);

                $person = Person::where('user_id', $user->id)->orderByDesc('id')->first();
                $person->update($validated);

                return redirect()->route('profile.step', 3);



            case 3:
                $rules = [
                    'phone' => 'required|string|max:20',
                    'address' => 'required|string|max:255',
                ];

                if ($type !== 'person') {
                    $rules['education'] = 'required|in:مسير,مدرب,لاعب,آخر';
                }

                $validated = $request->validate($rules);

                $person = Person::where('user_id', $user->id)->orderByDesc('id')->first();
                $person->update($validated);
                
                
                Dossier::updateOrCreate(
    ['person_id' => $person->id],
    [
        'etat'        => 'pending',
        
        'owner_type'  => $type,
        'note_admin'  => '📌 تم رفع الوثائق وجاري التحقق منها',
    ]
);
                
                
                
                 $dossier = Dossier::where('person_id', $person->id)->first();
             //   return redirect()->route('profile.step', 4);

                //  return view('profile.steps',4, compact('dossier'));
              return redirect()
    ->route('profile.step', 4)
    ->with('dossier', $dossier);

case 4:

    // ================== استرجاع الشخص والملف ==================
    $person = Person::where('user_id', $user->id)
        ->orderByDesc('id')
        ->firstOrFail();

    $dossier = Dossier::where('person_id', $person->id)->first();

    // ================== حساب العمر ==================
    $age = \Carbon\Carbon::parse($person->birth_date)->age;
    $isMinor = $age < 18;

    // ================== الوثائق الموجودة مسبقًا ==================
    $existingAttachments = ($dossier && $dossier->attachments)
        ? (is_array($dossier->attachments)
            ? $dossier->attachments
            : json_decode($dossier->attachments, true))
        : [];

    // ================== Helper required / nullable ==================
    $req = function ($key, $rule) use ($existingAttachments) {
        return empty($existingAttachments[$key])
            ? "required|$rule"
            : "nullable|$rule";
    };

    // ================== Validation ديناميكي ==================
   /* $rules = [
        // للجميع
        'medical_certificate' => $req(
            'medical_certificate',
            'file|mimes:pdf,jpg,jpeg,png|max:4096'
        ),

        // 👈 التعهّد للجميع
        'engagement' => $req(
            'engagement',
            'file|mimes:pdf,jpg,png|max:4096'
        ),

        // 👈 الصورة للجميع
        'photo' => $req(
            'photo',
            'image|mimes:jpg,jpeg,png|max:2048'
        ),
    ];

    if ($isMinor) {
        $rules += [
            'birth_certificate' => $req(
                'birth_certificate',
                'file|mimes:pdf|max:4096'
            ),
            'parental_authorization' => $req(
                'parental_authorization',
                'file|mimes:pdf,jpg,png|max:4096'
            ),
            'guardian_id_card' => $req(
                'guardian_id_card',
                'file|mimes:pdf,jpg,png|max:4096'
            ),
        ];
    } else {
        $rules += [
            'national_id_card' => $req(
                'national_id_card',
                'file|mimes:pdf,jpg,png|max:4096'
            ),
        ];
    }*/
    $rules = [
    'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
    'engagement'          => 'nullable|file|mimes:pdf,jpg,png|max:4096',
    'photo'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
];

if ($isMinor) {
    $rules['birth_certificate'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096';
    $rules['parental_authorization'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096';
    $rules['guardian_id_card'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096';
} else {
    $rules['national_id_card'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096';
}
    
    $messages = [
    'photo.max' => '❌ حجم الصورة كبير جدًا. الحد الأقصى المسموح به هو 2 ميغابايت.',
    'photo.mimes' => '❌ يجب أن تكون الصورة بصيغة JPG أو JPEG أو PNG فقط.',
    'photo.image' => '❌ الملف المرفوع ليس صورة صالحة.',

    'medical_certificate.max' => '❌ شهادة طبية: الحد الأقصى للملف هو 4 ميغابايت.',
    'engagement.max' => '❌ التعهد: الحد الأقصى للملف هو 4 ميغابايت.',
];

//dd($_FILES);
    $request->validate($rules, $messages);

    // ================== تحديد مسار التخزين ==================
    if (app()->environment('local')) {
        $storagePath = storage_path('app/public');
        $storageUrl  = '/storage';
    } else {
        $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
        $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
    }

    // ================== Helper رفع الملفات ==================
    $upload = function ($field, $folder) use ($request, $storagePath, $storageUrl) {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move($storagePath . '/' . $folder, $fileName);

        return $storageUrl . '/' . $folder . '/' . $fileName;
    };

    // ================== دمج الوثائق القديمة + الجديدة ==================
    $attachments = $existingAttachments;

    // للجميع
    if ($path = $upload('medical_certificate', 'documents')) {
        $attachments['medical_certificate'] = $path;
    }

    if ($path = $upload('engagement', 'documents')) {
        $attachments['engagement'] = $path;
    }

    if ($path = $upload('photo', 'photos')) {
        $attachments['photo'] = $path;
        $person->photo = $path;
    }

    if ($isMinor) {

        if ($path = $upload('birth_certificate', 'documents')) {
            $attachments['birth_certificate'] = $path;
            $person->birth_certificate = $path;
        }

        if ($path = $upload('parental_authorization', 'documents')) {
            $attachments['parental_authorization'] = $path;
        }

        if ($path = $upload('guardian_id_card', 'documents')) {
            $attachments['guardian_id_card'] = $path;
        }

    } else {

        if ($path = $upload('national_id_card', 'documents')) {
            $attachments['national_id_card'] = $path;
        }
    }

    // ================== حفظ الشخص ==================
    $person->save();

    // ================== حفظ dossier ==================
$existingDossier = Dossier::where('person_id', $person->id)->first();

$newEtat = ($existingDossier && $existingDossier->etat === 'approved')
    ? 'approved'
    : 'pending';

Dossier::updateOrCreate(
    ['person_id' => $person->id],
    [
        'etat'        => $newEtat,
        'attachments' => json_encode($attachments, JSON_UNESCAPED_UNICODE),
        'owner_type'  => $type,
        'note_admin'  => '📌 تم رفع الوثائق وجاري التحقق منها',
    ]
);


    // ================== التوجيه النهائي ==================
    $route = match ($user->type) {
        'admin'   => 'admin.dashboard',
        'club'    => 'club.dashboard',
        'company' => 'entreprise.dashboard',
        default   => 'person.dashboard',
    };

    return redirect()
        ->route($route)
        ->with('success', '✔ تم استكمال البيانات بنجاح 🎉');

break;



            //    return redirect()->route($route)->with('success','✔ تم استكمال البيانات بنجاح 🎉');
        }
    }
public function newPerson()
{
    // حذف وضع التعديل
    session()->forget('edit_person_id');

    return redirect()->route('profile.step', 1);
}


    /* =====================================================
       🛠️ دوال التعديل الجديدة (Club & Entreprise only)
    ===================================================== */

   public function editStep($personId, $step)
{
    $user = Auth::user();

    // التحقق من أن الشخص تابع فعلاً للمستخدم (نادي/مؤسسة)
    $person = Person::where('id', $personId)
                    ->where('user_id', $user->id)
                    ->firstOrFail();

    // 🧠 تخزين الـ ID في Session للعمل في وضع تعديل
    session(['edit_person_id' => $person->id]);

    // جلب بيانات الخطوة
    return redirect()->route('profile.step', $step)
                     ->with('info', '📝 وضع التعديل مفعل!');
}



    public function saveEditStep(Request $request, $personId, $step)
    {
        $person = Person::findOrFail($personId);

        switch ($step) {
            case 1:
                $person->update($request->validate([
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'birth_date' => 'required|date',
                    'gender' => 'required'
                ]));
                break;

            case 2:
                $person->update($request->validate([
                    'parent_firstname' => 'nullable|string',
                    'parent_lastname' => 'nullable|string',
                    'parent_phone' => 'nullable'
                ]));
                break;

            case 3:
                $person->update($request->validate([
                    'phone' => 'required|string',
                    'address' => 'required|string',
                    'education' => 'nullable'
                ]));
                break;
        }

        return redirect()
            ->route('profile.editStep', ['person' => $personId, 'step' => $step + 1])
            ->with('success','✔ تم تحديث البيانات بنجاح');
    }
}
