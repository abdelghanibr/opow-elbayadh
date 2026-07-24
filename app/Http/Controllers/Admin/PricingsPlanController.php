<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\Activity;
use App\Models\AgeCategory;
use Illuminate\Http\Request;

class PricingsPlanController extends Controller
{
    public function index()
    {
        $plans = PricingPlan::with(['activity', 'ageCategory'])->orderBy('id', 'DESC')->get();
        return view('admin.pricing_plans.index', compact('plans'));
    }

    public function create()
    {
        $activities = Activity::orderBy('title')->get();
        $categories = AgeCategory::orderBy('name')->get();
        return view('admin.pricing_plans.create', compact('activities', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'age_category_id' => 'required|exists:age_categories,id',
            'name' => 'required|string|max:255',
            'pricing_type' => 'nullable|string',
            'duration_unit' => 'nullable|string',
            'duration_value' => 'nullable|integer|min:1|max:24',
            'price' => 'required|numeric|min:0',
            'sessions_per_week' => 'nullable|integer|min:0|max:14',
            'sexe' => 'nullable|string',
            'type_client' => 'nullable|string',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'active' => 'nullable|boolean'
        ]);

        PricingPlan::create($request->all());

        return redirect()->route('admin.pricing_plans.index')
            ->with('success', '✔ تم إضافة خطة التسعير بنجاح');
    }

    public function edit($id)
    {
        $plan = PricingPlan::findOrFail($id);
        $activities = Activity::orderBy('title')->get();
        $categories = AgeCategory::orderBy('name')->get();
        return view('admin.pricing_plans.edit', compact('plan', 'activities', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $plan = PricingPlan::findOrFail($id);

        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'age_category_id' => 'required|exists:age_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'active' => 'nullable|boolean'
        ]);

        $plan->update($request->all());

        return redirect()->route('admin.pricing_plans.index')
            ->with('success', '✔ تم تحديث خطة التسعير بنجاح');
    }

    public function destroy($id)
    {
        PricingPlan::findOrFail($id)->delete();

        return redirect()->route('admin.pricing_plans.index')
            ->with('success', '🗑 تم حذف خطة التسعير');
    }
}
