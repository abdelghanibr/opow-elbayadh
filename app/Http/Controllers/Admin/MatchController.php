<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchModel;
use App\Models\Team;
use App\Models\Complex;
use App\Models\ComplexSeat ;
use Illuminate\Http\Request;
use App\Models\SeatType;
use App\Models\Ticket;
use Carbon\Carbon;

class MatchController extends Controller
{
  /*  public function index()
    {
        $matches = MatchModel::with(['homeTeam', 'awayTeam', 'complex'])->get();
        return view('admin.matches.index', compact('matches'));
    }*/
    
    public function index()
{
    $admin = auth()->user();

    // الاستعلام الأساسي مع العلاقات
    $query = MatchModel::with(['homeTeam', 'awayTeam', 'complex']);

    // إذا كان المدير مرتبط بمجمّع معيّن → عرض مباريات هذا المجمّع فقط
    if (!is_null($admin->complex_id) && $admin->complex_id != 0) {

        $query->where('complex_id', $admin->complex_id);

        $matches = $query->get();

        $complexes = Complex::where('id', $admin->complex_id)->get();

    } else {
        // إذا لم يكن مرتبطًا بمجمّع → عرض جميع المباريات
        $matches = $query->get();
        $complexes = Complex::all();
    }

    return view('admin.matches.index', compact('matches', 'complexes'));
}


public function publicMatches()
{
    $matches = MatchModel::with(['homeTeam', 'awayTeam', 'complex'])
        ->whereIn('status', ['active', 'scheduled'])
        ->whereDate('match_date', '>=', Carbon::today())
        ->orderBy('match_date')
        ->get();

    return view('admin.matches.public', compact('matches'));
}

/*public function publicMatches()
{
    $matches = MatchModel::with(['homeTeam','awayTeam','complex'])
        ->where('status', 'active')
        ->orWhere('status', 'scheduled')
        ->orderBy('match_date')
        ->get();

    return view('admin.matches.public', compact('matches'));
}*/
/*public function selectSeat($match_id)
{
    $match = MatchModel::with(['homeTeam','awayTeam','complex'])->findOrFail($match_id);
    $seatTypes = SeatType::all();

    return view('admin.tickets.select-seat', compact('match','seatTypes'));
}*/

public function selectSeat($match_id)
{
    $match = MatchModel::with(['homeTeam', 'awayTeam', 'complex'])->findOrFail($match_id);

    // Fetch seats related to this complex
    $complexSeats = ComplexSeat::where('complex_id', $match->complex_id)
        ->with('seatType')
        ->get();

    // Calculate sold tickets per seat type
    foreach ($complexSeats as $seat) {

      $sold = Ticket::where('match_id', $match_id)
              ->where('seat_type_id', $seat->seat_type_id)
              ->count();

        $total = $seat->total_seats ?? $seat->available_seats ?? 0;
        $seat->remaining = max(0, $total - $sold);
    }
//dd($complexSeats) ;
return view('admin.tickets.select-seat', compact('match', 'complexSeats'));
}

  /*  public function create()
    {
        $teams = Team::all();
        $complexes = Complex::all();

        return view('admin.matches.create', compact('teams', 'complexes'));
    }*/
    public function create()
{
    $admin = auth()->user();

    // تحميل الفرق (لا علاقة لها بالمجمع في هذه المرحلة)
    $teams = Team::all();

    // إذا كان المدير مرتبط بمجمع معيّن → أظهر له فقط هذا المجمع
    if (!is_null($admin->complex_id) && $admin->complex_id != 0) {

        $complexes = Complex::where('id', $admin->complex_id)->get();

    } else {

        // المدير العام → يرى كل المجمعات
        $complexes = Complex::all();
    }

    return view('admin.matches.create', compact('teams', 'complexes'));
}


   public function store(Request $request)
{
    $messages = [

        // الفرق
        'team_home_id.required' => 'يجب اختيار الفريق المستضيف.',
        'team_away_id.required' => 'يجب اختيار الفريق الضيف.',
        'team_away_id.different' => 'لا يمكن اختيار نفس الفريق كفريق مستضيف وضيف في نفس الوقت.',

        // المجمع
        'complex_id.required' => 'يجب اختيار المنشأة الرياضية التي ستُلعب فيها المباراة.',

        // التاريخ
        'match_date.required' => 'يرجى إدخال تاريخ المباراة.',
        'match_date.date' => 'تنسيق تاريخ المباراة غير صحيح.',
        'match_date.after_or_equal' => 'لا يمكن وضع تاريخ المباراة في الماضي، يجب اختيار تاريخ اليوم أو تاريخ لاحق.',

        // الوقت
        'match_time.required' => 'يرجى إدخال وقت المباراة.',

        // الحالة
        'status.required' => 'يجب اختيار حالة المباراة.',
        'status.string' => 'تنسيق حالة المباراة غير صحيح.',
    ];

    $rules = [
        'team_home_id' => 'required',
        'team_away_id' => 'required|different:team_home_id',
        'complex_id' => 'required',
        'match_date' => 'required|date|after_or_equal:today',
        'match_time' => 'required',
        'status' => 'required|string',
    ];

    $validated = $request->validate($rules, $messages);

    // حفظ البيانات
    MatchModel::create($validated);

    return redirect()->route('matches.index')
                     ->with('success', '✔️ تم إنشاء المباراة بنجاح.');
}


  /*  public function edit(MatchModel $match)
    {
        $teams = Team::all();
        $complexes = Complex::all();

        return view('admin.matches.edit', compact('match', 'teams', 'complexes'));
    }*/
    public function edit(MatchModel $match)
{
    $admin = auth()->user();

    // 🚨 حماية: منع الوصول لمباراة ليست ضمن مجمع المسؤول
    if (!is_null($admin->complex_id) && $admin->complex_id != 0) {
        if ($match->complex_id != $admin->complex_id) {
            abort(403, 'غير مسموح لك بتعديل مباراة تابعة لمجمع آخر');
        }
    }

    // تحميل قائمة الفرق
    $teams = Team::all();

    // إذا كان المدير مرتبط بمجمع معيّن → عرض مجمعه فقط
    if (!is_null($admin->complex_id) && $admin->complex_id != 0) {
        $complexes = Complex::where('id', $admin->complex_id)->get();
    } else {
        // المدير العام يرى جميع المجمعات
        $complexes = Complex::all();
    }

    return view('admin.matches.edit', compact('match', 'teams', 'complexes'));
}


    public function update(Request $request, MatchModel $match)
    {
        $request->validate([
            'team_home_id' => 'required',
            'team_away_id' => 'required|different:team_home_id',
            'match_date' => 'required|date',
            'match_time' => 'required',
             'status' => 'required|string',
        ]);

        $match->update($request->all());

        return redirect()->route('matches.index')
                         ->with('success', 'Match mis à jour');
    }

    public function destroy(MatchModel $match)
    {
        $match->delete();

        return redirect()->route('matches.index')
                         ->with('success', 'Match supprimé');
    }
}
