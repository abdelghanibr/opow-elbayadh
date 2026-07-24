<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::all();
        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        return view('admin.teams.create');
    }

   public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    /* ===== Gestion du stockage ===== */
    if (app()->environment('local')) {
        // Environnement local (public/storage)
        $storagePath = storage_path('app/public/teams');
        $storageUrl  = '/storage/teams';
    } else {
        // Environnement production (public_html/...)
        $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/') . '/teams';
        $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/') . '/teams';
    }

    // Créer le dossier si nécessaire
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
    }

    $logoPath = null;

    // ----- Upload du logo -----
    if ($request->hasFile('logo')) {

        $fileName = uniqid('team_') . '.' . $request->logo->getClientOriginalExtension();

        // Déplacer le fichier
        $request->logo->move($storagePath, $fileName);

        // URL qui sera stockée dans la base
        $logoPath = $storageUrl . '/' . $fileName;
    }

    // ----- Enregistrer dans la BD -----
    Team::create([
        'name' => $request->name,
        'logo' => $logoPath,
    ]);

    return redirect()->route('teams.index')
        ->with('success', '✔ الفريق تم تسجيله بنجاح');
}

    public function edit(Team $team)
    {
        return view('admin.teams.edit', compact('team'));
    }

  public function update(Request $request, Team $team)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    /* ===== Gestion du stockage ===== */
    if (app()->environment('local')) {
        $storagePath = storage_path('app/public/teams');
        $storageUrl  = '/storage/teams';
    } else {
        $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/') . '/teams';
        $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/') . '/teams';
    }

    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
    }

    $logoPath = $team->logo; // conserver l’ancien logo si aucun nouveau n’est uploadé

    // ----- Upload nouveau logo -----
    if ($request->hasFile('logo')) {

        // supprimer l'ancien fichier si existe
        if ($team->logo && file_exists(public_path($team->logo))) {
            @unlink(public_path($team->logo));
        }

        // nouveau nom unique
        $fileName = uniqid('team_') . '.' . $request->logo->getClientOriginalExtension();

        // déplacement
        $request->logo->move($storagePath, $fileName);

        // chemin utilisable
        $logoPath = $storageUrl . '/' . $fileName;
    }

    // Mise à jour BD
    $team->update([
        'name' => $request->name,
        'logo' => $logoPath,
    ]);

    return redirect()->route('teams.index')
        ->with('success', '✔ تم تحديث بيانات الفريق بنجاح');
}

    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()->route('teams.index')
                         ->with('success', 'Équipe supprimée');
    }
}
