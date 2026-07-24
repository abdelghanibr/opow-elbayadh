<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityCategory;

class ActivityController extends Controller
{
    /**
     * عرض جميع الأنشطة
     */
 /*public function index()
{
    $activities = Activity::with('activityCategory')->get();
    return view('activities.index', compact('activities'));
}*/
/*/public function index(Request $request)
{
    $query = Activity::query();

    // 🔍 البحث
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // 🧩 الفلترة حسب الفئة
    if ($request->filled('category_id')) {
        $query->where('activity_category_id', (int)$request->category_id);
    }

    $activities = $query->latest()->get();

    // 🧩 جميع الفئات
    $categories = ActivityCategory::orderBy('name')->get();

    return view('activities.index', compact('activities', 'categories'));
}*/
public function index(Request $request)
{
    $userComplexId = auth()->user()->complex_id;

    // نبدأ فقط بالأنشطة المربوطة بالمجمع الخاص بالمستخدم
    $query = Activity::whereHas('complexActivities', function ($q) use ($userComplexId) {
        $q->where('complex_id', $userComplexId);
    });

    // 🔍 البحث
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // 🧩 الفلترة حسب الفئة
    if ($request->filled('category_id')) {
        $query->where('activity_category_id', (int)$request->category_id);
    }

    $activities = $query->latest()->get();

    // 🧩 جميع الفئات
    $categories = ActivityCategory::orderBy('name')->get();

    return view('activities.index', compact('activities', 'categories'));
}

    /**
     * صفحة إضافة نشاط
     */

public function create()
{
   // $ageCategories      = AgeCategory::orderBy('name')->get();
    $activities = Activity::latest()->get();
    $activityCategories = ActivityCategory::orderBy('name')->get();

    return view('admin.activities.create', compact(
      
        'activityCategories'
    ));
}


    /**
     * حفظ النشاط الجديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icon'  => 'required|image|mimes:jpg,jpeg,png|max:4096',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'color' => 'nullable|string',
            'activity_category_id' => 'nullable|exists:activity_categories,id'
   
        ]);

        // المسارات من .env
        $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
        $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');

        $iconUrl = null;

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $iconName = time() . '_' . $file->getClientOriginalName();
            $file->move($storagePath . '/photos', $iconName);

            $iconUrl = $storageUrl . '/photos/' . $iconName;
        }

        Activity::create([
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'color' => $request->color,
            'icon' => $iconUrl,
            'activity_category_id' => $request->activity_category_id
        ]);

        return redirect()
            ->route('activities.index')
            ->with('success', 'تمت إضافة النشاط بنجاح 🎉');
    }

    /**
     * عرض المركبات الخاصة بنشاط معين
     */
    public function complexes($id)
    {
        session(['activity_id' => $id]);
        $activity = Activity::with('complexes')->findOrFail($id);
        return view('activities.complexes', compact('activity'));
    }

    /**
     * تسجيل المستخدم في نشاط
     */
    public function register($id)
    {
        DB::table('activity_user')->insert([
            'user_id' => Auth::id(),
            'activity_id' => $id,
            'status' => 'en_attente',
            'payment_status' => 'non_paye',
            'created_at' => now(),
        ]);

        return back()->with('success', 'تم تسجيلك بنجاح وهو قيد الدراسة');
    }
public function select(Request $request)
{
    $request->validate([
        'activity_id' => 'required|integer|exists:activities,id',
    ]);

    // حفظ النشاط في الـ Session
    session(['activity_id' => $request->activity_id]);

    // التوجيه إلى صفحة الحجز (كما كنت تفعل سابقاً)
    return redirect()->route('reservation.form', auth()->user()->complex_id);
}

    /**
     * عرض أنشطة المستخدم
     */
    public function myActivities()
    {
        $my = DB::table('activity_user')
            ->join('activities', 'activities.id', '=', 'activity_user.activity_id')
            ->where('activity_user.user_id', Auth::id())
            ->select('activities.*', 'activity_user.status', 'activity_user.payment_status')
            ->get();

        return view('activities.my', compact('my'));
    }
}
