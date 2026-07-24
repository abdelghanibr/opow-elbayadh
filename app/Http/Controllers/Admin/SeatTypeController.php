<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeatType;
use Illuminate\Http\Request;

class SeatTypeController extends Controller
{
    public function index()
    {
        $types = SeatType::all();
        return view('admin.seat_types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.seat_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'price' => 'required|numeric'
        ]);

        SeatType::create($request->all());

        return redirect()->route('seat_types.index')
                         ->with('success', 'Type de siège ajouté');
    }

    public function edit(SeatType $seat_type)
    {
        return view('admin.seat_types.edit', compact('seat_type'));
    }

    public function update(Request $request, SeatType $seat_type)
    {
        $request->validate([
            'name'  => 'required',
            'price' => 'required|numeric'
        ]);

        $seat_type->update($request->all());

        return redirect()->route('seat_types.index')
                         ->with('success', 'Type mis à jour');
    }

    public function destroy(SeatType $seat_type)
    {
        $seat_type->delete();

        return redirect()->route('seat_types.index')
                         ->with('success', 'Type supprimé');
    }
}
