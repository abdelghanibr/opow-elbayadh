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
use App\Services\SimpleXlsx;

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
        'blood_type'  => 'nullable|string|max:5|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        'address'     => 'nullable|string|max:255',
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
    $person->blood_type = $validated['blood_type'] ?? null;
    $person->address    = $validated['address'] ?? null;
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
        'blood_type'  => 'nullable|string|max:5|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        'address'     => 'nullable|string|max:255',
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

    /* فقط إذا أرسل النموذج الحقلين (حماية من المسح عند نماذج لا تحتويهما) */
    $cardFields = array_intersect_key($validated, ['blood_type' => 1, 'address' => 1]);
    if ($cardFields) {
        $person->update($cardFields);
    }

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

    /* =====================================================
       📥 IMPORT EN MASSE (modèle Excel)
    ===================================================== */
    public function checkDuplicate(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->type, ['company', 'club'], true)) {
            abort(403, 'نوع المستخدم غير مدعوم');
        }

        $firstname = trim((string) $request->query('firstname'));
        $lastname  = trim((string) $request->query('lastname'));
        $birthDate = $this->parseDate($request->query('birth_date'));
        $excludeId = (int) $request->query('exclude_id');

        if ($firstname === '' || $lastname === '' || !$birthDate) {
            return response()->json(['exists' => false]);
        }

        $query = Person::where('user_id', $user->id)
            ->where('firstname', $firstname)
            ->where('lastname', $lastname)
            ->whereDate('birth_date', $birthDate);

        if ($excludeId > 0) {
            $query->where('id', '!=', $excludeId);
        }

        return response()->json(['exists' => $query->exists()]);
    }

    public function downloadImportTemplate()
    {
        $user = Auth::user();

        if (!in_array($user->type, ['company', 'club'], true)) {
            abort(403, 'نوع المستخدم غير مدعوم');
        }

        $headers = [
            'الاسم', 'اللقب', 'تاريخ الميلاد', 'الجنس', 'التصنيف',
        ];

        // Repli CSV si l'extension zip n'est pas disponible (xlsx impossible)
        if (!class_exists(\ZipArchive::class)) {
            $csv = "\xEF\xBB\xBF" . implode(';', $headers) . "\r\n";

            return response()->streamDownload(function () use ($csv) {
                echo $csv;
            }, 'modele_import_personnes.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        try {
            $content = SimpleXlsx::build($headers, [], 'الأشخاص');
        } catch (\Throwable $e) {
            abort(500, $e->getMessage());
        }

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'modele_import_personnes.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->type, ['company', 'club'], true)) {
            abort(403, 'نوع المستخدم غير مدعوم');
        }

        $request->validate([
            'file' => 'required|file|max:2048',
        ]);

        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['xlsx', 'csv', 'txt'], true)) {
            return back()->withErrors(['file' => 'الصيغة غير مدعومة. يرجى رفع ملف Excel (xlsx) أو CSV.']);
        }

        /* ===== ربط المؤسسة/النادي ===== */
        if ($user->type === 'club') {
            $club = $user->club;
        } else {
            $club = Club::where('user_id', $user->id)->first();
        }

        if (!$club) {
            return back()->withErrors(['file' => 'لا توجد مؤسسة/نادي مرتبط بهذا الحساب.']);
        }

        /* ===== قراءة الملف ===== */
        try {
            $rows = in_array($ext, ['csv', 'txt'], true)
                ? $this->readCsvRows($file->getRealPath())
                : SimpleXlsx::read($file->getRealPath());
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'تعذر قراءة الملف: ' . $e->getMessage()]);
        }

        if (empty($rows)) {
            return back()->withErrors(['file' => 'الملف فارغ.']);
        }

        array_shift($rows); // صف العناوين

        $genders    = ['ذكر', 'أنثى'];
        $educations = ['لاعب', 'مدرب', 'مسير', 'آخر'];

        $imported = 0;
        $errors   = [];
        $seen     = [];
        $valid    = [];

        // تحميل مسبق للأشخاص الموجودين (لكشف التكرار بدون استعلام لكل سطر)
        $existing = Person::where('user_id', $user->id)
            ->get(['firstname', 'lastname', 'birth_date'])
            ->mapWithKeys(function ($p) {
                $key = mb_strtolower(trim((string) $p->firstname)) . '|'
                    . mb_strtolower(trim((string) $p->lastname)) . '|'
                    . ($p->birth_date ? Carbon::parse($p->birth_date)->format('Y-m-d') : '');
                return [$key => true];
            })
            ->all();

        /* ===== المرحلة 1: التحقق من كامل الملف (بدون أي إدخال) ===== */
        foreach ($rows as $i => $row) {
            $line = $i + 2;

            $row = array_map(fn ($v) => is_string($v) ? trim($v) : $v, (array) $row);

            $nonEmpty = array_filter($row, fn ($v) => trim((string) $v) !== '');
            if (empty($nonEmpty)) {
                continue;
            }

            $firstname = trim((string) ($row[0] ?? ''));
            $lastname  = trim((string) ($row[1] ?? ''));
            $birthRaw  = $row[2] ?? '';
            $gender    = trim((string) ($row[3] ?? ''));
            $education = trim((string) ($row[4] ?? ''));

            $rowErrors = [];

            if ($firstname === '') {
                $rowErrors[] = 'الاسم مطلوب';
            } elseif (mb_strlen($firstname) > 100) {
                $rowErrors[] = 'الاسم طويل جداً';
            }

            if ($lastname === '') {
                $rowErrors[] = 'اللقب مطلوب';
            } elseif (mb_strlen($lastname) > 100) {
                $rowErrors[] = 'اللقب طويل جداً';
            }

            $birthDate = $this->parseDate($birthRaw);
            if (!$birthDate) {
                $rowErrors[] = 'تاريخ الميلاد غير صالح (استعمل YYYY-MM-DD)';
            } elseif (Carbon::parse($birthDate)->gt(now()->subYears(3))) {
                $rowErrors[] = 'يجب أن يكون العمر 3 سنوات على الأقل';
            }

            if (!in_array($gender, $genders, true)) {
                $rowErrors[] = 'الجنس يجب أن يكون ذكر أو أنثى';
            }

            if ($education === '') {
                $rowErrors[] = 'التصنيف مطلوب';
            } elseif (!in_array($education, $educations, true)) {
                $rowErrors[] = 'التصنيف غير صالح (لاعب/مدرب/مسير/آخر)';
            }

            if ($rowErrors) {
                $errors[] = 'السطر ' . $line . ': ' . implode('، ', $rowErrors);
                continue;
            }

            // كشف التكرار: نفس الاسم + اللقب + تاريخ الميلاد (داخل الملف أو في قاعدة البيانات)
            $dupKey = mb_strtolower($firstname) . '|' . mb_strtolower($lastname) . '|' . $birthDate;

            if (isset($seen[$dupKey])) {
                $errors[] = 'السطر ' . $line . ': ' . $firstname . ' ' . $lastname . ' — مكرر داخل الملف';
                continue;
            }

            if (isset($existing[$dupKey])) {
                $errors[] = 'السطر ' . $line . ': ' . $firstname . ' ' . $lastname . ' — موجود مسبقاً في قاعدة البيانات';
                continue;
            }

            $seen[$dupKey] = true;

            $valid[] = [
                'line'       => $line,
                'firstname'  => $firstname,
                'lastname'   => $lastname,
                'birth_date' => $birthDate,
                'gender'     => $gender,
                'education'  => $education,
            ];
        }

        /* ===== إذا وُجد أي خطأ أو تكرار: إيقاف الاستيراد وعرض التقرير فقط ===== */
        if (!empty($errors)) {
            session()->flash('import_blocked', true);
            session()->flash('import_success_count', 0);
            session()->flash('import_errors', $errors);

            return redirect()->route(
                $user->type === 'club' ? 'club.persons.index' : 'entreprise.persons.index'
            );
        }

        /* ===== المرحلة 2: الاستيراد (الملف سليم) ===== */
        foreach ($valid as $v) {
            try {
                $person             = new Person();
                $person->user_id    = $user->id;
                $person->firstname  = $v['firstname'];
                $person->lastname   = $v['lastname'];
                $person->birth_date = $v['birth_date'];
                $person->gender     = $v['gender'];
                $person->education  = $v['education'];
                $person->club_id    = $club->id;
                $person->save();

                $imported++;
            } catch (\Throwable $e) {
                $errors[] = 'السطر ' . $v['line'] . ': خطأ أثناء الحفظ';
            }
        }

        session()->flash('import_blocked', false);
        session()->flash('import_success_count', $imported);
        session()->flash('import_errors', $errors);

        return redirect()->route(
            $user->type === 'club' ? 'club.persons.index' : 'entreprise.persons.index'
        );
    }

    private function readCsvRows(string $path): array
    {
        $rows = [];

        if (($handle = fopen($path, 'r')) === false) {
            return $rows;
        }

        $firstLine = fgets($handle);
        rewind($handle);

        $delim = ',';
        if ($firstLine !== false) {
            $counts = [
                ','  => substr_count($firstLine, ','),
                ';'  => substr_count($firstLine, ';'),
                "\t" => substr_count($firstLine, "\t"),
            ];
            arsort($counts);
            $delim = array_key_first($counts);
        }

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        while (($data = fgetcsv($handle, 0, $delim)) !== false) {
            $rows[] = $data;
        }

        fclose($handle);

        return $rows;
    }

    private function parseDate($value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // Excel serial date
        if (is_numeric($value) && (float) $value > 1000 && (float) $value < 60000) {
            $ts = ((float) $value - 25569) * 86400;

            return gmdate('Y-m-d', (int) round($ts));
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'Y/m/d'] as $format) {
            try {
                $date = Carbon::createFromFormat('!' . $format, $value);
                if ($date && $date->format($format) === $value) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                // try next format
            }
        }

        $ts = strtotime($value);

        return $ts !== false ? date('Y-m-d', $ts) : null;
    }
}
