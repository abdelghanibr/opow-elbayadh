<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\AgeCategory;
use App\Models\Club;
use App\Models\Complex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Dossier;


class PersonsController extends Controller
{
    /* ===================== INDEX ===================== */



public function index()
{
    $admin = Auth::user();

    $query = Person::with([
            'ageCategory',
            'club',
            'dossier',
            'user',
            'user.latestReservation',
        ])
        ->whereNotNull('firstname')
        ->whereNotNull('lastname')
        ->where('firstname', '!=', '')
        ->where('lastname', '!=', '')
        ->whereHas('user', function ($q) {
            $q->where('type', 'person');
        })
        ->whereHas('dossier', function ($q) {
            $q->where('etat', 'approved');
        });

    if (is_null($admin->complex_id) || $admin->complex_id == 0) {
        $persons = $query->orderByDesc('id')->get();
        $complexes = Complex::orderBy('nom')->get();
    } else {
        $persons = $query
            ->whereHas('user', function ($q) use ($admin) {
                $q->where('complex_id', $admin->complex_id);
            })
            ->orderByDesc('id')
            ->get();

        $complexes = Complex::where('id', $admin->complex_id)->get();
    }

    $ageCategories = AgeCategory::orderBy('min_age')->get();

    return view('admin.persons.index', compact('persons', 'complexes', 'ageCategories'));
}


/*
  public function index()
    {
        $admin = Auth::user();

        $query = Person::with(['ageCategory', 'club', 'user', 'dossier'])
            ->whereNotNull('firstname')
            ->whereNotNull('lastname')
            ->where('firstname', '!=', '')
            ->where('lastname', '!=', '')
            ->whereHas('user', function ($q) {
                $q->where('type', 'person');
            })
            ->whereHas('dossier', function ($q) {
                $q->where('etat', 'approved');
            });

        if (is_null($admin->complex_id) || $admin->complex_id == 0) {
            $persons = $query->orderByDesc('id')->get();
            $complexes = Complex::orderBy('nom')->get();
        } else {
            $persons = $query
                ->whereHas('user', function ($q) use ($admin) {
                    $q->where('complex_id', $admin->complex_id);
                })
                ->orderByDesc('id')
                ->get();

            $complexes = Complex::where('id', $admin->complex_id)->get();
        }

        $ageCategories = AgeCategory::orderBy('min_age')->get();

        return view('admin.persons.index', compact('persons', 'complexes', 'ageCategories'));
    }


*/
 

    /* ===================== CREATE ===================== */
    public function create()
    {
        $ageCategories = AgeCategory::orderBy('name')->get();
        $clubs         = Club::orderBy('name')->get();

        return view('admin.persons.create', compact(
            'ageCategories',
            'clubs'
        ));
    }

    /* ===================== STORE ===================== */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname'        => 'required|string|max:255',
            'lastname'         => 'required|string|max:255',
            'gender'           => 'nullable|string|max:20',
            'birth_date'       => 'nullable|date',
            'age_category_id'  => 'nullable|exists:age_categories,id',
            'club_id'          => 'nullable|exists:clubs,id',
            'phone'            => 'nullable|string|max:20',
            'wilaya'           => 'nullable|string|max:100',
            'handicap'         => 'nullable|boolean',
        ]);

        Person::create($validated);

        return redirect()
            ->route('persons.index')
            ->with('success', '✅ تم إضافة الشخص بنجاح');
    }

    /* ===================== EDIT ===================== */
    public function edit(Person $person)
    {
        $ageCategories = AgeCategory::orderBy('name')->get();
        $clubs         = Club::orderBy('name')->get();

        return view('admin.persons.edit', compact(
            'person',
            'ageCategories',
            'clubs'
        ));
    }

    /* ===================== UPDATE ===================== */
    public function update(Request $request, Person $person)
    {
        $validated = $request->validate([
            'firstname'        => 'required|string|max:255',
            'lastname'         => 'required|string|max:255',
            'gender'           => 'nullable|string|max:20',
            'birth_date'       => 'nullable|date',
            'age_category_id'  => 'nullable|exists:age_categories,id',
            'club_id'          => 'nullable|exists:clubs,id',
            'phone'            => 'nullable|string|max:20',
            'wilaya'           => 'nullable|string|max:100',
            'handicap'         => 'nullable|boolean',
        ]);

        $person->update($validated);

        return redirect()
            ->route('persons.index')
            ->with('success', '✏️ تم تحديث بيانات الشخص بنجاح');
    }









public function destroy($id)
{
    $person = Person::findOrFail($id);

    $linkedUser = User::find($person->user_id);

    // récupérer les dossiers liés à la personne
    $dossiers = Dossier::where('person_id', $person->id)->get();

    // préparer la liste des fichiers à supprimer
    $filesToDelete = [];

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

    DB::transaction(function () use ($dossiers, $person, $linkedUser) {
        foreach ($dossiers as $dossier) {
            $dossier->delete();
        }

        $person->delete();

        if ($linkedUser) {
            $linkedUser->delete();
        }
    });

    foreach ($filesToDelete as $filePath) {
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    return redirect()
        ->route('persons.index')
        ->with('success', '✅ تم حذف الشخص والمستخدم والملفات المرتبطة بنجاح');
}    /* ===================== DESTROY (اختياري) ===================== */
  /*  public function destroy(Person $person)
    {
        $person->delete();
dd($person);
        return redirect()
            ->route('persons.index')
            ->with('success', '🗑️ تم حذف الشخص');
    }*/
    
 /*   public function destroy($id)
{
    $authUser = Auth::user();
    $person   = Person::findOrFail($id);

    if ($person->user_id !== $authUser->id) {
        abort(403);
    }
dd($id);
    $linkedUser = User::find($person->user_id);

    // récupérer les dossiers liés à la personne
    $dossiers = Dossier::where('person_id', $person->id)->get();

    // préparer la liste des fichiers à supprimer
    $filesToDelete = [];

    foreach ($dossiers as $dossier) {
        if (!empty($dossier->attachments)) {
            $attachments = json_decode($dossier->attachments, true);

            // si attachments est un JSON contenant plusieurs fichiers
            if (is_array($attachments)) {
                foreach ($attachments as $file) {
                    if (!empty($file)) {
                        $filesToDelete[] = $file;
                    }
                }
            } else {
                // si attachments contient un seul chemin
                $filesToDelete[] = $dossier->attachments;
            }
        }
    }

    DB::transaction(function () use ($dossiers, $person, $linkedUser) {
        // supprimer les dossiers
        foreach ($dossiers as $dossier) {
            $dossier->delete();
        }

        // supprimer la personne
        $person->delete();

        // supprimer l'utilisateur lié dans users
        if ($linkedUser) {
            $linkedUser->delete();
        }
    });

    // supprimer les fichiers physiques après suppression des lignes
    foreach ($filesToDelete as $filePath) {
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    return back()->with('success', '✅ Utilisateur, personne, dossiers et fichiers supprimés avec succès.');
}
*/
}
