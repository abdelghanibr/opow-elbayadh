<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index()
    {
        $seasons = Season::orderByDesc('id')->get();
        return view('admin.seasons.index', compact('seasons'));
    }

    public function create()
    {
        $types = Season::TYPES;
        return view('admin.seasons.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'type_season' => 'required|in:'.implode(',', array_keys(Season::TYPES)),
            'date_debut'  => 'nullable|date',
            'date_fin'    => 'nullable|date|after_or_equal:date_debut',
        ]);

        Season::create($data);

        return redirect()
            ->route('seasons.index')
            ->with('success', '✅ تم إنشاء الموسم بنجاح');
    }

    public function edit(Season $season)
    {
        $types = Season::TYPES;
        return view('admin.seasons.edit', compact('season','types'));
    }

    public function update(Request $request, Season $season)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'type_season' => 'required|in:'.implode(',', array_keys(Season::TYPES)),
            'date_debut'  => 'nullable|date',
            'date_fin'    => 'nullable|date|after_or_equal:date_debut',
        ]);

        $season->update($data);

        return redirect()
            ->route('seasons.index')
            ->with('success', '✅ تم تحديث الموسم بنجاح');
    }

    public function destroy(Season $season)
    {
        $season->delete();

        return back()->with('success', '🗑️ تم حذف الموسم');
    }
}
