<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ComplexeController extends Controller
{
    /**
     * عرض قائمة المركبات + صفحة الإدارة
     */
    public function index()
 
     
{
    $user = Auth::user();
    $complexes = DB::table('complexes')->orderBy('id', 'DESC')->get();

    if ($user->type === 'admin') {
        return view('admin.complexes.index', compact('complexes'));
    }

    return view('complexes.index', compact('complexes'));
}

    public function create()
    {
        return view('admin.complexes.create');
    }

    /**
     * تخزين مركب جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
//dd($request) ;
        DB::table('complexes')->insert([
            'nom' => $request->nom,
            'capacite' => $request->capacite,
            'prix' => $request->prix,
            'type' => $request->type,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', '✔ تم إضافة المركب بنجاح');
    }
public function edit($id)
{
    $complex = Complex::findOrFail($id);

    return view('admin.complexes.edit', compact('complex'));
}

    /**
     * تعديل مركب
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::table('complexes')->where('id', $id)->update([
            'nom' => $request->nom,
            'capacite' => $request->capacite,
            'prix' => $request->prix,
            'type' => $request->type,
            'description' => $request->description,
            'updated_at' => now(),
        ]);

        return back()->with('success', '✏️ تم تحديث بيانات المركب بنجاح');
    }

    /**
     * حذف مركب
     */
    public function destroy($id)
    {
        DB::table('complexes')->where('id', $id)->delete();
        return back()->with('success', '🗑 تم حذف المركب بنجاح');
    }
}
