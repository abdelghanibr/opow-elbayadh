<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;



use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Dossier;

class PersonController extends Controller
{
    /* =====================================================
       📄 INDEX
    ===================================================== */
    public function index()
    {
        $user = Auth::user();

        $views = [
            'club'     => 'club.persons.index',
            'company'  => 'entreprise.persons.index',
            'person'   => 'person.profile',
        ];

        if (!isset($views[$user->type])) {
            abort(403, 'Unauthorized');
        }

        $persons = Person::where('user_id', $user->id)
            ->orderByDesc('id')
            ->get();

        return view($views[$user->type], compact('persons'));
    }
  
  
    public function byOwner($id)
{
    // جلب المستخدم صاحب النادي أو المؤسسة
    $owner = User::findOrFail($id);

    // جلب الأشخاص المرتبطين بهذا المستخدم
    $persons = Person::with(['ageCategory', 'club', 'user'])
                     ->where('user_id', $owner->id)
                     ->orderByDesc('id')
                     ->get();

    return view('admin.persons.by_owner', compact('owner', 'persons'));
}

/*public function index()
{
    $user = Auth::user();

    // تحديد نوع الواجهة حسب نوع المستخدم
    $views = [
        'club'     => 'club.persons.index',
        'company'  => 'entreprise.persons.index',
        'person'   => 'person.profile',
        'admin'    => 'admin.persons.index',
    ];

    if (!isset($views[$user->type])) {
        abort(403, 'Unauthorized');
    }

    // إذا كان admin عادي وليس مرتبط بمجمع → عرض كل الأشخاص
    if (empty($user->complex_id) || $user->complex_id == 0) {
        $persons = Person::with('user')->orderByDesc('id')->get();
    } else {
        // إذا كان admin خاص بمجمع → عرض فقط الأشخاص المرتبطين بهذا المجمع
        $persons = Person::whereHas('user', function ($q) use ($user) {
            $q->where('complex_id', $user->complex_id);
        })
        ->orderByDesc('id')
        ->get();
    }

    return view($views[$user->type], compact('persons'));
}*/
public function printSelected(Request $request)
{
    $ids = $request->input('ids'); // بدل selected_ids

    if (!$ids) {
        return back()->with('error', '❌ لم يتم اختيار أي شخص للطباعة');
    }

    $idsArray = explode(',', $ids);

    $persons = Person::whereIn('id', $idsArray)->get();

    Person::whereIn('id', $idsArray)->update(['etat_ass' => 1]);

    return view('admin.persons.print', compact('persons'));
}


public function updateAssurance(Request $request)
{
    Person::whereIn('id', $request->ids)
        ->update(['etat_ass' => 1]); // مؤمّن

    return response()->json(['success' => true]);
}

    /* =====================================================
       ➕ CREATE
    ===================================================== */
    public function create()
    {
        $user = Auth::user();

        return match ($user->type) {
            'club'    => view('club.persons.create'),
            'company' => view('entreprise.persons.create'),
            default   => abort(403, 'نوع المستخدم غير مدعوم'),
        };
    }

    /* =====================================================
       💾 STORE
    ===================================================== */
  public function store(Request $request)
{
    $user = Auth::user();

    // 1️⃣ القواعد الأساسية
    $rules = [
        'firstname'  => 'required|string|max:100',
        'lastname'   => 'required|string|max:100',
        'birth_date' => 'required|date|before_or_equal:' . now()->subYears(3)->format('Y-m-d'),
        'gender'     => 'required|in:ذكر,أنثى',
        'education'  => 'required|string|max:50',
        'study_level' => 'nullable|string|max:100|in:فئة المدارس او البراعم,فئة اقل من 13 سنة (U13),فئة اقل من 16 سنة (U16),فئة اقل من 20 سنة (U20),فئة الاكابر',
        'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'registration_form_pdf' => 'nullable|file|mimes:pdf|max:3072',
    ];

    // 2️⃣ شرط رقم الإجازة إذا كان المستخدم نادي فقط
    if ($user->type === 'club') {
        $rules['license_number'] = 'required|string|max:50|unique:persons,license_number';
    } else {
        $rules['license_number'] = 'nullable|string|max:50';
    }

    // 3️⃣ تنفيذ التحقق
    $validated = $request->validate($rules);

    /* ===== Storage path (Local / Production) ===== */
    if (app()->environment('local')) {
        $storagePath = storage_path('app/public');
        $storageUrl  = '/storage';
    } else {
        $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
        $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
    }

    /* ===== Upload Photo ===== */
    $photoPath = null;

    if ($request->hasFile('photo')) {

        $directory = $storagePath . '/photos/persons';

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file     = $request->file('photo');
        $filename = uniqid('person_') . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        $photoPath = $storageUrl . '/photos/persons/' . $filename;
    }

    /* ===== Upload Registration Form PDF ===== */
    $birthCertificatePath = null;

    if ($request->hasFile('registration_form_pdf')) {

        $pdfDir = $storagePath . '/pdfs/persons';

        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }

        $pdfFile     = $request->file('registration_form_pdf');
        $pdfFilename = uniqid('form_') . '.' . $pdfFile->getClientOriginalExtension();
        $pdfFile->move($pdfDir, $pdfFilename);

        $birthCertificatePath = $storageUrl . '/pdfs/persons/' . $pdfFilename;
    }

    /* ===== Create Person ===== */
    $person = new Person();
    $person->user_id    = $user->id;
    $person->firstname  = $validated['firstname'];
    $person->lastname   = $validated['lastname'];
    $person->birth_date = $validated['birth_date'];
    $person->gender     = $validated['gender'];
    $person->education  = $validated['education'];
    $person->study_level = $validated['study_level'] ?? null;
    $person->photo      = $photoPath;
    $person->birth_certificate = $birthCertificatePath;
    $person->license_number = $validated['license_number'] ?? null;

    /* ===== Link to Club (Club or Company) ===== */
    if ($user->type === 'club') {

        if (!$user->club) {
            abort(403, 'لا يوجد نادي مرتبط بالحساب');
        }

        $person->club_id = $user->club->id;

    } elseif ($user->type === 'company') {

        $club = Club::where('user_id', $user->id)->first();

        if (!$club) {
            abort(403, 'لا توجد مؤسسة مرتبطة بالحساب');
        }

        $person->club_id = $club->id;

    } else {
        abort(403, 'نوع المستخدم غير مدعوم');
    }

    $person->save();

    return redirect()
        ->route(
            $user->type === 'club'
                ? 'club.persons.index'
                : 'entreprise.persons.index'
        )
        ->with('success', '✅ تم الحفظ بنجاح');
}


    /* =====================================================
       ✏️ EDIT
    ===================================================== */
    public function edit($id)
    {
        $user   = Auth::user();
        $person = Person::findOrFail($id);

              if ((int)$person->user_id !== (int)auth()->id()) {
   abort(403, 'غير مصرح لك بتعديل هذا الشخص');
}

        return match ($user->type) {
            'club'    => view('club.persons.edit', compact('person')),
            'company' => view('entreprise.persons.edit', compact('person')),
            default   => abort(403),
        };
    }

    /* =====================================================
       🔄 UPDATE
    ===================================================== */
   public function update(Request $request, $id)
{
    $user   = Auth::user();
    $person = Person::findOrFail($id);

    if ((int)$person->user_id !== (int)$user->id) {
        abort(403);
    }

    $rules = [
        'firstname'  => 'required|string|max:100',
        'lastname'   => 'required|string|max:100',
        'birth_date' => 'required|date|before_or_equal:' . now()->subYears(3)->format('Y-m-d'),
        'gender'     => 'required|in:ذكر,أنثى',
        'education'   => 'required|string|max:50',
        'study_level' => 'nullable|string|max:100|in:فئة المدارس او البراعم,فئة اقل من 13 سنة (U13),فئة اقل من 16 سنة (U16),فئة اقل من 20 سنة (U20),فئة الاكابر',
        'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'registration_form_pdf' => 'nullable|file|mimes:pdf|max:3072',
    ];

    if ($user->type === 'club') {
        $rules['license_number'] =
            'required|string|max:50|unique:persons,license_number,' . $person->id;
    } else {
        $rules['license_number'] = 'nullable|string|max:50';
    }

    $validated = $request->validate($rules);

    if (app()->environment('local')) {
        $storagePath = storage_path('app/public');
        $storageUrl  = '/storage';
    } else {
        $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
        $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
    }

    $person->update([
        'firstname'      => $validated['firstname'],
        'lastname'       => $validated['lastname'],
        'birth_date'     => $validated['birth_date'],
        'gender'         => $validated['gender'],
        'education'      => $validated['education'],
        'study_level'    => $validated['study_level'] ?? null,
        'license_number' => $validated['license_number'] ?? null,
    ]);

    if ($request->hasFile('photo')) {

        if ($person->photo) {
            $oldPhotoRelative = str_replace('/storage', '', $person->photo);
            $oldPhotoFull = $storagePath . $oldPhotoRelative;

            if (file_exists($oldPhotoFull)) {
                unlink($oldPhotoFull);
            }
        }

        $directory = $storagePath . '/photos/persons';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file     = $request->file('photo');
        $filename = uniqid('person_') . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        $photoPath = $storageUrl . '/photos/persons/' . $filename;

        $person->update([
            'photo' => $photoPath
        ]);
    }

    if ($request->hasFile('registration_form_pdf')) {

        if ($person->birth_certificate) {
            $oldPdfRelative = str_replace('/storage', '', $person->birth_certificate);
            $oldPdfFull = $storagePath . $oldPdfRelative;

            if (file_exists($oldPdfFull)) {
                unlink($oldPdfFull);
            }
        }

        $pdfDir = $storagePath . '/pdfs/persons';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }

        $pdfFile     = $request->file('registration_form_pdf');
        $pdfFilename = uniqid('form_') . '.' . $pdfFile->getClientOriginalExtension();
        $pdfFile->move($pdfDir, $pdfFilename);

        $person->update([
            'birth_certificate' => $storageUrl . '/pdfs/persons/' . $pdfFilename,
        ]);
    }

    return redirect()
        ->route(
            $user->type === 'club'
                ? 'club.persons.index'
                : 'entreprise.persons.index'
        )
        ->with('success', '✔ تم تحديث البيانات بنجاح');
}


    /* =====================================================
       🗑 DELETE
    ===================================================== */
/*    public function destroy($id)
    {
        $user   = Auth::user();
        $person = Person::findOrFail($id);

        if ($person->user_id !== $user->id) {
            abort(403);
        }

        $person->delete();

        return back()->with('success', '❌ تم حذف المستخدم بنجاح');
    }*/






public function destroy($id)
{
    $authUser = Auth::user();
    $person   = Person::findOrFail($id);

    if ((int)$person->user_id !== (int)$authUser->id) {
        abort(403);
    }

    $dossiers = Dossier::where('person_id', $person->id)->get();

    $filesToDelete = [];

    if (!empty($person->photo)) {
        $filesToDelete[] = $person->photo;
    }

    if (!empty($person->birth_certificate)) {
        $filesToDelete[] = $person->birth_certificate;
    }

    foreach ($dossiers as $dossier) {
        if (!empty($dossier->attachments)) {
            $attachments = json_decode($dossier->attachments, true);
            if (is_array($attachments)) {
                foreach ($attachments as $file) {
                    if (!empty($file)) {
                        $filesToDelete[] = $file;
                    }
                }
            } else {
                $filesToDelete[] = $dossier->attachments;
            }
        }
    }

    DB::transaction(function () use ($dossiers, $person) {
        foreach ($dossiers as $dossier) {
            $dossier->delete();
        }
        $person->delete();
    });

    foreach ($filesToDelete as $filePath) {
        $localPath = str_replace('/storage', '', $filePath);
        $fullPath = app()->environment('local')
            ? storage_path('app/public' . $localPath)
            : rtrim(env('PUBLIC_STORAGE_PATH'), '/') . $localPath;

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    return back()->with('success', '✅ تم حذف الشخص وجميع ملفاته بنجاح.');
}
}
