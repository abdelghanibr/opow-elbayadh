<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Person;
use App\Models\Dossier;
use App\Models\User;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index()
    {
        return redirect()->route('profile.step', 1);
    }

    /**
     * 👤 Personne "de base" du compte (le parent lui-même)
     */
    private function accountParentPerson(User $user): ?Person
    {
        return Person::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Personne active dans le wizard :
     * - club/entreprise → personne en session (ou nouvelle)
     * - person → le parent lui-même, ou l'enfant en cours d'édition
     *
     * @return array{0: Person|null, 1: bool} [personne, isChild]
     */
    private function activePerson(User $user): array
    {
        if ($user->type === 'club' || $user->type === 'company') {
            if (session()->has('edit_person_id')) {
                return [Person::find(session('edit_person_id')), false];
            }
            return [new Person(), false];
        }

        $parentPerson = $this->accountParentPerson($user);

        $activePersonId = session('edit_person_id');
        if ($activePersonId) {
            $person = Person::where('id', $activePersonId)
                ->where(function ($q) use ($user, $parentPerson) {
                    $q->where('user_id', $user->id);
                    if ($parentPerson) {
                        $q->orWhere('parent_id', $parentPerson->id);
                    }
                })
                ->first();

            if ($person) {
                return [$person, $person->isChild()];
            }
        }

        return [$parentPerson, false];
    }

    /**
     * 👶 Page "Mes enfants" (comptes person uniquement)
     */
    public function children()
    {
        $user = Auth::user();

        $parentPerson = Person::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->orderByDesc('id')
            ->first();

        $children = $parentPerson
            ? Person::where('parent_id', $parentPerson->id)
                ->with(['ageCategory', 'dossier'])
                ->orderByDesc('id')
                ->get()
            : collect();

        return view('person.children', compact('parentPerson', 'children'));
    }

    /**
     * ➕ Créer un enfant (personne vide rattachée au parent)
     */
    public function storeChild(Request $request)
    {
        $user = Auth::user();

        if ($user->type !== 'person') {
            abort(403);
        }

        $parentPerson = Person::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->orderByDesc('id')
            ->firstOrFail();

        $child = Person::create([
            'user_id'    => null,
            'parent_id'  => $parentPerson->id,
        ]);

        session(['edit_person_id' => $child->id]);

        return redirect()
            ->route('profile.step', 1)
            ->with('info', '👶 أضف الآن معلومات طفلك (الخطوات 1 إلى 4).');
    }

    /**
     * ✏️ Passer en mode édition d'un enfant
     */
    public function editChild($personId)
    {
        $user = Auth::user();

        $child = Person::where('id', $personId)
            ->whereHas('parent', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        session(['edit_person_id' => $child->id]);

        return redirect()
            ->route('profile.step', 1)
            ->with('info', '📝 وضع تعديل معلومات الطفل مفعل.');
    }

    /**
     * 🎟️ Réserver un siège pour un enfant (dossier approuvé requis)
     */
    public function reserveChild($personId)
    {
        $user = Auth::user();

        $child = Person::where('id', $personId)
            ->whereHas('parent', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('dossier', 'ageCategory')
            ->firstOrFail();

        if (!$child->dossier || $child->dossier->etat !== 'approved') {
            return back()->with('error', '⚠️ لا يمكن حجز مقعد لهذا الطفل قبل مصادقة ملفه من الإدارة.');
        }

        session(['booking_person_id' => $child->id]);
        session()->forget('activity_id');

        return redirect()
            ->route('activities.index')
            ->with('info', '👶 الحجز لصالح ' . ($child->firstname ?? 'الطفل') . ' ' . ($child->lastname ?? '') . ' — اختر النشاط ثم المركب ثم الفوج.');
    }

public function showStep($step)
{
    $user = Auth::user();

    [$person, $isChild] = $this->activePerson($user);

    $parentPerson = null;
    if ($user->type === 'person') {
        $parentPerson = $this->accountParentPerson($user);
    }

    // 👶 Un enfant n'a pas besoin de saisir les infos du tuteur (étape 2 = le parent)
    if ($isChild && $step == 2) {
        return redirect()->route('profile.step', 3);
    }

    $dossier = $person ? Dossier::where('person_id', $person->id)->first() : null;

    $wilayas = [
        "أدرار","الشلف","الأغواط","أم البواقي","باتنة","بجاية","بسكرة","بشار",
        "البليدة","البويرة","تمنراست","تبسة","تلمسان","تيارت","تيزي وزو","الجزائر",
        "الجلفة","جيجل","سطيف","سعيدة","سكيكدة","سيدي بلعباس","عنابة","قالمة",
        "قسنطينة","المدية","مستغانم","المسيلة","معسكر","ورقلة","وهران","البيض",
        "إليزي","برج بوعريريج","بومرداس","الطارف","تندوف","تيسمسيلت","الوادي",
        "خنشلة","سوق أهراس","تيبازة","البيض","عين الدفلى","النعامة","عين تموشنت",
        "غرداية","غليزان"
    ];

    return view('profile.steps', compact('step','user','person','wilayas','dossier','parentPerson','isChild'));
}



    public function saveStep(Request $request, $step)
    {
        $user = Auth::user();
        $type = $user->type;

        [$person, $isChild] = $this->activePerson($user);

        $parentPerson = $this->accountParentPerson($user);

        $dossier = $person ? Dossier::where('person_id', $person->id)->first() : null;
    
        switch ($step) {

            case 1:
                $tuteurRule = $isChild ? 'required|string|max:50' : 'nullable|string|max:50';

                $validated = $request->validate([
                    'firstname' => 'required|string|max:50',
                    'lastname' => 'required|string|max:50',
                  'birth_date' => 'required|date|before:-3 years',
                    'gender' => 'required',
                    'blood_type' => 'nullable|string|max:5',
                    'profession' => 'nullable|string|max:100',
                    'handicap' => 'required' ,
                    'tuteur_fullname' =>  $tuteurRule,
                ]);

                $age = Carbon::parse($request->birth_date)->age;
                $ageCat = $age <= 12 ? 1 : ($age <= 17 ? 2 : ($age <= 100 ? 3 : 4));

                if ($isChild) {
                    if ($age >= 18) {
                        $childName = trim(($person->firstname ?? '') . ' ' . ($person->lastname ?? ''));
                        $person->delete();
                        session()->forget('edit_person_id');
                        return redirect()->route('children.index')
                            ->with('success', '✔ تم حذف ' . ($childName ?: 'الطفل') . ' تلقائياً — عمره 18 سنة فأكثر، لا يمكن تسجيله كطفل.');
                    }

                    $person->update(array_merge($validated, ['age_category_id' => $ageCat]));
                } elseif ($type === 'club' || $type === 'company') {
                    Person::create(array_merge($validated, [
                        'user_id' => $user->id,
                        'age_category_id' => $ageCat
                    ]));
                } else {
                    Person::updateOrCreate(
                        ['user_id' => $user->id, 'parent_id' => null],
                        array_merge($validated, ['age_category_id' => $ageCat])
                    );
                }

                return redirect()->route('profile.step', ($age < 18 ? 2 : 3));



            case 2:
                if ($type !== 'person') return redirect()->route('profile.step', 3);

                if ($isChild) {
                    // 👶 Les infos du tuteur = le parent connecté (pas de re-saisie)
                    $person->update([
                        'parent_firstname' => $parentPerson->firstname ?? '',
                        'parent_lastname'  => $parentPerson->lastname ?? '',
                        'parent_phone'     => $parentPerson->phone ?? '',
                    ]);
                } else {
                    $validated = $request->validate([
                        'parent_firstname' => 'required|string|max:50',
                        'parent_lastname' => 'required|string|max:50',
                        'parent_phone' => 'required|string|max:20',
                    ]);

                    $person->update($validated);
                }

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

                if ($isChild) {
                    // 👶 Pré-remplir avec les infos du parent si vides
                    $person->update([
                        'phone'   => $validated['phone'] ?: ($parentPerson->phone ?? ''),
                        'address' => $validated['address'] ?: ($parentPerson->address ?? ''),
                    ]);
                } else {
                    $person->update($validated);
                }
                
                
                Dossier::updateOrCreate(
    ['person_id' => $person->id],
    [
        'etat'        => 'pending',
        
        'owner_type'  => $type,
        'note_admin'  => '📌 تم رفع الوثائق وجاري التحقق منها',
    ]
);
                
                
                
                 $dossier = Dossier::where('person_id', $person->id)->first();

              return redirect()
    ->route('profile.step', 4)
    ->with('dossier', $dossier);

case 4:

    // ================== استرجاع الشخص والملف ==================
    if (!$person) {
        return redirect()->route('profile.step', 1);
    }

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

    // ================== وثائق الولي المشتركة (guardian_docs) ==================
    // 👶 Un enfant hérite des docs du tuteur ; une seule saisie suffit pour tous les enfants
    $guardianDocsOwner = $isChild ? $parentPerson : ($type === 'person' ? $person : null);

    if ($guardianDocsOwner) {
        $guardianDocs = is_array($guardianDocsOwner->guardian_docs)
            ? $guardianDocsOwner->guardian_docs
            : [];

        // copier les docs du parent vers l'enfant s'ils manquent
        if ($isChild) {
            foreach (['guardian_id_card', 'parental_authorization'] as $k) {
                if (empty($attachments[$k]) && !empty($guardianDocs[$k])) {
                    $attachments[$k] = $guardianDocs[$k];
                }
            }
        }

        // propager les docs envoyés vers le compte parent (partagés avec tous les enfants)
        foreach (['guardian_id_card', 'parental_authorization'] as $k) {
            if (!empty($attachments[$k])) {
                $guardianDocs[$k] = $attachments[$k];
            }
        }

        if ($guardianDocs) {
            $guardianDocsOwner->guardian_docs = $guardianDocs;
            $guardianDocsOwner->save();
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
    if ($isChild) {
        session()->forget('edit_person_id');

        return redirect()
            ->route('children.index')
            ->with('success', '✔ تم حفظ ملف الطفل بنجاح 🎉');
    }

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

    // التحقق من أن الشخص تابع فعلاً للمستخدم (نادي/مؤسسة) أو ابن تابع للوالد
    $person = Person::where('id', $personId)
        ->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('parent', function ($p) use ($user) {
                  $p->where('user_id', $user->id);
              });
        })
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
