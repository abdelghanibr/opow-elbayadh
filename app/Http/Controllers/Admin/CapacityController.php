<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplexActivity as Capacity;
use App\Models\Complex;
use App\Models\Activity;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CapacityController extends Controller
{
/*    public function index()
    {
        $capacities = Capacity::with(['complex', 'activity'])->get();
        return view('admin.capacities.index', compact('capacities'));
    }*/
public function index()
{
    $admin = Auth::user();

    // إذا كان admin عام (complex_id فارغ أو = 0) → عرض الجميع
    if (is_null($admin->complex_id) || $admin->complex_id == 0) {
        $capacities = Capacity::with(['complex', 'activity'])->get();
    } 
    else {
        // Admin خاص بمجمع → عرض فقط capacities الخاصة بالمجمع
        $capacities = Capacity::with(['complex', 'activity'])
            ->where('complex_id', $admin->complex_id)
            ->get();
    }

    return view('admin.capacities.index', compact('capacities'));
}

  /*  public function create()
    {
        $complexes = Complex::all();
        $activities = Activity::all();
        $seasons = Season::all();

        return view('admin.capacities.create', compact('complexes', 'activities'));
    }*/
public function create()
{
    $admin = Auth::user();

    if (!is_null($admin->complex_id) && $admin->complex_id != 0) {
        // عرض مجمع واحد فقط
        $complexes = Complex::where('id', $admin->complex_id)->get();

        // الأنشطة الخاصة بالمجمع (عبر جدول complex_activity)
   $activities = Activity::all();

    } else {
        // admin الرئيسي
        $complexes = Complex::all();
        $activities = Activity::all();
    }

    return view('admin.capacities.create', compact('complexes', 'activities'));
}

    public function store(Request $request)
    {
        $request->validate([
            'complex_id' => 'required',
            'activity_id' => 'required',
          //  'season_id' => 'required',
            'capacity' => 'required|integer|min:0',
        ]);

        Capacity::create($request->all());

        return redirect()->route('admin.capacities.index')->with('success', 'تم إضافة السعة بنجاح');
    }

    /*public function edit($id)
    {
        $capacity = Capacity::findOrFail($id);
        $complexes = Complex::all();
        $activities = Activity::all();
        $seasons = Season::all();

        return view('admin.capacities.edit', compact('capacity', 'complexes', 'activities'));
    }*/
public function edit($id)
{
    $admin = Auth::user();
    $capacity = Capacity::findOrFail($id);

    // إذا كان المدير مربوط بمجمّع
    if (!is_null($admin->complex_id) && $admin->complex_id != 0) {

        // تحميل فقط مجمّعه
        $complexes = Complex::where('id', $admin->complex_id)->get();

        // تحميل الأنشطة المرتبطة بهذا المجمّع فقط
     $activities = Activity::all();

        // المواسم العادية
        $seasons = Season::all();

    } else {

        // مدير عام → يشوف كل شيء
        $complexes = Complex::all();
        $activities = Activity::all();
        $seasons = Season::all();
    }

    return view('admin.capacities.edit', compact(
        'capacity',
        'complexes',
        'activities',
        'seasons'
    ));
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'complex_id' => 'required',
            'activity_id' => 'required',
         //   'season_id' => 'required',
            'capacity' => 'required|integer|min:0',
        ]);

        $capacity = Capacity::findOrFail($id);
        $capacity->update($request->all());

        return redirect()->route('admin.capacities.index')->with('success', 'تم تحديث السعة بنجاح');
    }

    public function destroy($id)
    {
        Capacity::findOrFail($id)->delete();
        return redirect()->route('admin.capacities.index')->with('success', 'تم الحذف بنجاح');
    }
}
