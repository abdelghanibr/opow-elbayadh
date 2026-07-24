<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityCategory;
use Illuminate\Http\Request;

class ActivityCategoryController extends Controller
{
    public function index()
    {
        $categories = ActivityCategory::latest()->get();
        return view('admin.activity_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.activity_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
           
            'color' => 'nullable|string|max:20',
        ]);

        ActivityCategory::create($request->all());

        return redirect()
            ->route('activity-categories.index')
            ->with('success', '✅ تم إضافة الصنف بنجاح');
    }

    public function edit(ActivityCategory $activityCategory)
    {
        return view('admin.activity_categories.edit', compact('activityCategory'));
    }

    public function update(Request $request, ActivityCategory $activityCategory)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            
            'color' => 'nullable|string|max:20',
        ]);

        $activityCategory->update($request->all());

        return redirect()
            ->route('activity-categories.index')
            ->with('success', '✏️ تم تعديل الصنف بنجاح');
    }

    public function destroy(ActivityCategory $activityCategory)
    {
        $activityCategory->delete();

        return redirect()
            ->route('activity-categories.index')
            ->with('success', '🗑️ تم حذف الصنف');
    }
}
