<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ComplexController extends Controller
{
    /**
     * 📌 عرض قائمة المركبات
     */
    public function index()
    {
        $complexes = Complex::orderBy('id', 'DESC')->get();
        return view('admin.complexes.index', compact('complexes'));
    }

    /**
     * 📌 عرض صفحة إضافة مركب جديد
     */
    public function create()
    {
        return view('admin.complexes.create');
    }

    /**
     * 📌 حفظ مركب جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'adresse'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:20',
            'capacite_mi'  => 'nullable|numeric',
             'capacite_ma'  => 'nullable|numeric',
                  'type'  => 'nullable|string|max:255',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
        ]);
//dd($request);
        /* ===== Gestion du stockage ===== */
        if (app()->environment('local')) {
            $storagePath = storage_path('app/public/complexes');
            $storageUrl  = '/storage/complexes';
        } else {
            $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/') . '/complexes';
            $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/') . '/complexes';
        }

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $fileName = uniqid('complex_') . '.' . $request->image->getClientOriginalExtension();
            $request->image->move($storagePath, $fileName);
            $imagePath = $storageUrl . '/' . $fileName;
        }

        Complex::create([
            'nom'       => $request->nom,
            'adresse'   => $request->adresse,
            'phone'     => $request->phone,
            'capacite_mi'  => $request->capacite_mi,
             'capacite_ma'  => $request->capacite_ma,
            'image'     => $imagePath,
              'type'     => $request->type,
                'user_id'      => Auth::id(), // ✅ هنا الحفظ
        ]);

        return redirect()->route('admin.complexes.index')
            ->with('success', '✔ تم إضافة المركب بنجاح');
    }

    /**
     * 📌 عرض صفحة تعديل مركب
     */
    public function edit($id)
    {
        $complex = Complex::findOrFail($id);
        return view('admin.complexes.edit', compact('complex'));
    }

    /**
     * 📌 تحديث بيانات مركب
     */
   public function update(Request $request, $id)
{
    $request->validate([
        'nom'       => 'required|string|max:255',
        'adresse'   => 'nullable|string|max:255',
        'phone'     => 'nullable|string|max:20',
        'capacite_mi'  => 'nullable|numeric',
           'capacite_ma'  => 'nullable|numeric',
              'type'  => 'nullable|string|max:255',
        'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $complex = Complex::findOrFail($id);

    /* ===== Gestion du stockage ===== */
    if (app()->environment('local')) {
        $storagePath = storage_path('app/public/complexes');
        $storageUrl  = '/storage/complexes';
    } else {
        $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/') . '/complexes';
        $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/') . '/complexes';
    }

    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
    }

    // 🖼 Gestion image
    if ($request->hasFile('image')) {

        // 🗑 حذف الصورة القديمة
        if ($complex->image) {
            $oldPath = str_replace($storageUrl, $storagePath, $complex->image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $fileName = uniqid('complex_') . '.' . $request->image->getClientOriginalExtension();
        $request->image->move($storagePath, $fileName);

        $complex->image = $storageUrl . '/' . $fileName;
    }

    // ✅ تحديث البيانات (بما فيها الصورة إن وُجدت)
    $complex->update([
        'nom'       => $request->nom,
        'adresse'   => $request->adresse,
        'phone'     => $request->phone,
        'capacite_mi'  => $request->capacite_mi,
             'capacite_ma'  => $request->capacite_ma,
           'type'     => $request->type,
        'image'     => $complex->image, // ⭐ المهم
      'user_id'      => Auth::id(),
    ]);

    return redirect()->route('admin.complexes.index')
        ->with('success', '✔ تم تحديث بيانات المركب بنجاح');
}

    /**
     * 📌 حذف مركب
     */
    public function destroy($id)
    {
        $complex = Complex::findOrFail($id);

        // 🗑 حذف الصورة من التخزين
        if ($complex->image) {
            if (app()->environment('local')) {
                $storagePath = storage_path('app/public/complexes');
                $storageUrl  = '/storage/complexes';
            } else {
                $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/') . '/complexes';
                $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/') . '/complexes';
            }

            $oldPath = str_replace($storageUrl, $storagePath, $complex->image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $complex->delete();

        return redirect()->route('admin.complexes.index')
            ->with('success', '🗑 تم حذف المركب بنجاح');
    }
}
