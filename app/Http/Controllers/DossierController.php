<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dossier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DossierController extends Controller
{
    public function upload()
    {
        return view('dossier.upload');
    }
public function updateNote(Request $request, Dossier $dossier)
{
    $request->validate([
        'note_admin' => 'required|string|max:1000',
    ]);

    $dossier->update([
        'note_admin' => $request->note_admin,
    ]);

    return back()->with('success', '✔ تم حفظ الملاحظة بنجاح');
}

public function autorisationParentale(Dossier $dossier)
{
    return view('dossier.autorisation-parentale', compact('dossier'));
}

private function authorizeDossierAccess(Dossier $dossier)
{
    $user = auth()->user();

    if (!$user) {
        abort(403, 'Accès non autorisé.');
    }

    /*
    |--------------------------------------------------------------------------
    | 1) Super admin / admin général : accès total
    |--------------------------------------------------------------------------
    */
    $role = $user->role ?? null;

    $isSuperAdmin = false;
    $isAdmin = false;

    if (method_exists($user, 'hasRole')) {
        $isSuperAdmin = $user->hasRole('super_admin');
        $isAdmin = $user->hasRole('admin');
    } else {
        $isSuperAdmin = in_array($role, ['super_admin', 'super-admin']);
        $isAdmin = in_array($role, ['admin']);
    }

    if ($isSuperAdmin) {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 2) Si le dossier appartient directement à l'utilisateur connecté
    |--------------------------------------------------------------------------
    */
    if (!empty($dossier->user_id) && (int) $dossier->user_id === (int) $user->id) {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 3) Charger la relation person.user si nécessaire
    |--------------------------------------------------------------------------
    */
    $dossier->loadMissing('person.user');

    /*
    |--------------------------------------------------------------------------
    | 4) Si le dossier est lié à une personne dont le user_id est l'utilisateur
    |--------------------------------------------------------------------------
    */
    if (
        $dossier->person &&
        !empty($dossier->person->user_id) &&
        (int) $dossier->person->user_id === (int) $user->id
    ) {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 5) Admin / directeur de complexe : voir seulement dossiers de son complexe
    |--------------------------------------------------------------------------
    */
    if ($isAdmin && !empty($user->complex_id) && (int) $user->complex_id !== 0) {
        if (
            $dossier->person &&
            $dossier->person->user &&
            (int) $dossier->person->user->complex_id === (int) $user->complex_id
        ) {
            return true;
        }

        abort(403, 'Accès refusé : ce dossier ne dépend pas de votre complexe.');
    }

    /*
    |--------------------------------------------------------------------------
    | 6) Admin sans complex_id : accès total
    |--------------------------------------------------------------------------
    */
    if ($isAdmin && (empty($user->complex_id) || (int) $user->complex_id === 0)) {
        return true;
    }

    abort(403, 'Accès non autorisé à ce dossier.');
}

public function print(Dossier $dossier)
{
    $this->authorizeDossierAccess($dossier);

    $dossier->loadMissing([
        'person',
        'person.user',
    ]);

    return view('dossier.print', compact('dossier'));
}





public function downloadFormulaire($id)
{
    try {
        $path = public_path('forms/formulaire-engagement.pdf');

        if (!file_exists($path)) {
            Log::error('PDF introuvable', ['path' => $path, 'id' => $id]);
            abort(404, 'PDF introuvable');
        }

        return response()->download($path, 'formulaire-engagement.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    } catch (\Throwable $e) {
        Log::error('Erreur downloadFormulaire', [
            'id' => $id,
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);

        abort(500, $e->getMessage());
    }
}


public function downloadAutorisationParentale($id)
{
    try {
        $path = public_path('forms/autorisation-parentale.pdf');
        // ou: $path = storage_path('app/public/forms/autorisation-parentale.pdf');

        if (!file_exists($path)) {
            abort(404, 'Fichier PDF introuvable : ' . $path);
        }

        return response()->download($path, 'autorisation-parentale.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    } catch (\Throwable $e) {
        Log::error('Erreur downloadAutorisationParentale', [
            'id' => $id,
            'message' => $e->getMessage(),
        ]);

        abort(500, 'Erreur serveur lors du téléchargement du PDF.');
    }
}
public function showFormulaire($dossierId)
{
    $dossier = \App\Models\Dossier::with(['person.user.complex'])->findOrFail($dossierId);

    return view('dossier.formulaire', compact('dossier'));
}

   public function store(Request $request)
{
    $request->validate([
        'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096'
    ]);

    if (!$request->hasFile('document')) {
        return back()->with('error', 'لم يتم اختيار أي ملف.');
    }

    $file = $request->file('document');
    $filename = time() . '_' . $file->getClientOriginalName();

    // ✔ رفع الملف فعليًا
    $file->move(public_path('uploads/dossiers'), $filename);

    // ✔ تحديث أو إنشاء الدوسيي (المشكل كان هنا)
    $dossier = Dossier::updateOrCreate(
        ['user_id' => auth()->id()],
        [
            'fichier' => $filename,
            'etat' => 'pending'
        ]
    );

    return back()->with('success', '✔ تم رفع الملف وتم تسجيله في قاعدة البيانات.');
}

/*public function index(Request $request)
{
    $query = Dossier::query();

    if ($request->has('owner_type') && $request->owner_type !== 'all') {
        $query->where('owner_type', $request->owner_type);
    }

    if ($request->has('etat') && $request->etat !== 'all') {
        $query->where('etat', $request->etat);
    }
$dossiers = Dossier::with('person')->get();

$dossiers->each(function ($d) {
    $d->age = $d->person && $d->person->birth_date
        ? \Carbon\Carbon::parse($d->person->birth_date)->age
        : null;
});

    $dossiers = $query->latest()->paginate(10);

    return view('admin.dossiers.index', compact('dossiers'));
}*/
public function index(Request $request)
{
    $admin = Auth::user();

    // ابدأ بالكويري الأساسي
   // $query = Dossier::query()->with('person');
    
    $query = Dossier::query()
    ->with('person')
    ->whereNotNull('attachments')
    ->where('attachments', '<>', '')
    ->where('attachments', '<>', '[]');
   
  //   $query = Dossier::query()
  //      ->with(['person', 'attachments'])
  //      ->whereHas('attachments'); // afficher seulement les dossiers qui ont des attachments

    // 🔍 تصفية حسب نوع المالك (اختياري)
    if ($request->filled('owner_type') && $request->owner_type !== 'all') {
        $query->where('owner_type', $request->owner_type);
    }

    // 🔍 تصفية حسب حالة الملف (اختياري)
    if ($request->filled('etat') && $request->etat !== 'all') {
        $query->where('etat', $request->etat);
    }

    // 🎯 تصفية حسب complex_id إذا كان admin مرتبط بمجمع
    if (!empty($admin->complex_id) && $admin->complex_id != 0) {

        $query->whereHas('person.user', function ($q) use ($admin) {
            $q->where('complex_id', $admin->complex_id);
        });

    }

    // 🔥 تنفيذ جلب النتائج مع pagination
    $dossiers = $query->latest()->paginate(1000000);

    // ➕ حساب العمر لكل شخص داخل الملفات
    $dossiers->each(function ($d) {
        $d->age = $d->person && $d->person->birth_date
            ? \Carbon\Carbon::parse($d->person->birth_date)->age
            : null;
    });

    return view('admin.dossiers.index', compact('dossiers'));
}




    public function status()
    {
        $dossier = Dossier::where('user_id', auth()->id())->firstOrFail();
        return view('dossier.status', compact('dossier'));
    }

    public function admin()
    {
        $dossiers = Dossier::with('user')->get();
        return view('dossier.admin', compact('dossiers'));
    }
    
    
    
    public function approve($id)
{
    $d = Dossier::findOrFail($id);

    $d->etat = 'approved';
    $d->validated_by = auth()->id();   // 👤 من صادق
    $d->validated_at = now();           // 🕒 متى صادق
    $d->save();

    return back()->with('success', 'تم قبول الملف بنجاح ✔');
}

public function reject($id)
{
    $d = Dossier::findOrFail($id);

    $d->etat = 'rejected';
    $d->validated_by = auth()->id();   // 👤 من رفض
    $d->validated_at = now();           // 🕒 متى رفض
    $d->save();

    return back()->with('error', 'تم رفض الملف ❌');
}


 /*public function approve($id)
{
    $d = Dossier::findOrFail($id);
    $d->etat = 'approved';
    $d->save();

    return back()->with('success', 'تم قبول الملف بنجاح ✔');
}

public function reject($id)
{
    $d = Dossier::findOrFail($id);
    $d->etat = 'rejected';
    $d->save();

    return back()->with('error', 'تم رفض الملف ❌');
}*/
public function person()
{
    return $this->belongsTo(Person::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}

}
