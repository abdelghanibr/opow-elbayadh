<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\Activity;
use App\Models\AgeCategory;
use Illuminate\Http\Request;

class PricingPlanController extends Controller
{
    public function index()
    {
        $pricingPlans = PricingPlan::with(['activity', 'ageCategory'])->get();
        return view('admin.pricing.index', compact('pricingPlans'));
    }

    public function create()
    {
        return view('admin.pricing.create', [
            'activities' => Activity::all(),
            'ageCategories' => AgeCategory::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'type_client' => 'required|in:person,club',
            'price' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
        ]);

        PricingPlan::create($request->all());

        return redirect()
            ->route('admin.pricing.index')
            ->with('success', '✔ تم إضافة خطة التسعير بنجاح');
    }

    public function destroy($id)
    {
        PricingPlan::findOrFail($id)->delete();
        return back()->with('success', '❌ تم حذف الخطة بنجاح');
    }
}
