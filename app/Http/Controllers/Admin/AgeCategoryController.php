<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgeCategory;
use Illuminate\Http\Request;

class AgeCategoryController extends Controller
{
    // 📋 عرض القائمة
    public function index()
    {
        $categories = AgeCategory::orderBy('min_age')->get();
        return view('admin.age_categories.index', compact('categories'));
    }

    // ➕ صفحة الإضافة
    public function create()
    {
        return view('admin.age_categories.create');
    }

    // 💾 حفظ
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'min_age'  => 'required|integer|min:0',
            'max_age'  => 'required|integer|gte:min_age',
        ]);

        AgeCategory::create($request->all());

        return redirect()
            ->route('age-categories.index')
            ->with('success', 'تمت إضافة الفئة العمرية بنجاح');
    }

    // ✏ تعديل
    public function edit(AgeCategory $ageCategory)
    {
        return view('admin.age_categories.edit', compact('ageCategory'));
    }

    // 🔄 تحديث
    public function update(Request $request, AgeCategory $ageCategory)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'min_age'  => 'required|integer|min:0',
            'max_age'  => 'required|integer|gte:min_age',
        ]);

        $ageCategory->update($request->all());

        return redirect()
            ->route('age-categories.index')
            ->with('success', 'تم تحديث الفئة العمرية بنجاح');
    }

    // 🗑 حذف
    public function destroy(AgeCategory $ageCategory)
    {
        $ageCategory->delete();

        return redirect()
            ->route('age-categories.index')
            ->with('success', 'تم حذف الفئة العمرية');
    }
}
