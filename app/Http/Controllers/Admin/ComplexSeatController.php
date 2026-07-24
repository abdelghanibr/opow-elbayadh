<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complex;
use App\Models\SeatType;
use App\Models\ComplexSeat;
use Illuminate\Http\Request;

class ComplexSeatController extends Controller
{
   /* public function index()
    {
        $complexSeats = ComplexSeat::with(['complex', 'seatType'])->get();
        return view('admin.complex_seats.index', compact('complexSeats'));
    }*/
    public function index()
{
    $admin = auth()->user();

    // إنشاء استعلام أساسي مع العلاقات
    $query = ComplexSeat::with(['complex', 'seatType']);

    // إذا كان المدير مرتبط بمجمّع → عرض مقاعد هذا المجمّع فقط
    if (!is_null($admin->complex_id) && $admin->complex_id != 0) {

        $query->where('complex_id', $admin->complex_id);

        $complexSeats = $query->get();

        $complexes = Complex::where('id', $admin->complex_id)->get();

    } else {
        // إذا لا يوجد complex_id → يمكنه رؤية الجميع
        $complexSeats = $query->get();

        $complexes = Complex::all();
    }

    return view('admin.complex_seats.index', compact('complexSeats', 'complexes'));
}


   /* public function create()
    {
        $complexes = Complex::all();
        $seatTypes = SeatType::all();
        return view('admin.complex_seats.create', compact('complexes', 'seatTypes'));
    }*/
public function create()
{
    $admin = auth()->user();

    // إذا كان المدير مرتبط بمجمع معين
    if (!is_null($admin->complex_id) && $admin->complex_id != 0) {

        // تحميل مجمع واحد فقط
        $complexes = Complex::where('id', $admin->complex_id)->get();

    } else {
        // المدير العام يرى جميع المجمعات
        $complexes = Complex::all();
    }

    // أنواع المقاعد
    $seatTypes = SeatType::all();

    return view('admin.complex_seats.create', compact('complexes', 'seatTypes'));
}

    public function store(Request $request)
    {
        $request->validate([
            'complex_id' => 'required',
            'seat_type_id' => 'required',
            'total_seats' => 'required|integer|min:1',
        ]);

        ComplexSeat::create([
            'complex_id' => $request->complex_id,
            'seat_type_id' => $request->seat_type_id,
            'total_seats' => $request->total_seats,
            'available_seats' => $request->total_seats
        ]);

        return redirect()->route('complex_seats.index')
                         ->with('success', 'Capacité ajoutée au complexe');
    }

  /*  public function edit(ComplexSeat $complexSeat)
    {
        $complexes = Complex::all();
        $seatTypes = SeatType::all();

        return view('admin.complex_seats.edit', compact('complexSeat', 'complexes', 'seatTypes'));
    }*/
public function edit(ComplexSeat $complexSeat)
{
    $admin = auth()->user();

    // إذا كان المدير مرتبط بمجمّع معين → أظهر له فقط هذا المجمّع
    if (!is_null($admin->complex_id) && $admin->complex_id != 0) {

        // 🔐 حماية إضافية: منع تعديل مقعد ليس في مجمّعه
        if ($complexSeat->complex_id != $admin->complex_id) {
            abort(403, 'غير مسموح لك بتعديل مقاعد مجمع آخر');
        }

        $complexes = Complex::where('id', $admin->complex_id)->get();

    } else {
        // المدير العام → يرى جميع المجمعات
        $complexes = Complex::all();
    }

    // أنواع المقاعد
    $seatTypes = SeatType::all();

    return view('admin.complex_seats.edit', compact('complexSeat', 'complexes', 'seatTypes'));
}

    public function update(Request $request, ComplexSeat $complexSeat)
    {
        $request->validate([
            'total_seats' => 'required|integer|min:1',
        ]);

        $complexSeat->update([
            'total_seats' => $request->total_seats,
            'available_seats' => $request->total_seats
        ]);

        return redirect()->route('complex_seats.index')
                         ->with('success', 'Capacité mise à jour');
    }

    public function destroy(ComplexSeat $complexSeat)
    {
        $complexSeat->delete();

        return redirect()->route('complex_seats.index')
                         ->with('success', 'Capacité supprimée');
    }
}
