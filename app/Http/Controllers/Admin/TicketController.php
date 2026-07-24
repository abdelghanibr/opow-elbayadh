<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\MatchModel;
use App\Models\SeatType;
use App\Models\Complex;

class TicketController extends Controller
{
   /* public function index()
    {
        $tickets = Ticket::with(['match', 'seatType'])->get();
        return view('admin.tickets.index', compact('tickets'));
    }*/
    
    public function index()
{
    $admin = auth()->user();

    // الاستعلام الأساسي مع العلاقات
    $query = Ticket::with(['match', 'seatType']);

    // إذا كان المدير مرتبط بمجمّع معين → عرض تذاكر هذا المجمّع فقط
    if (!is_null($admin->complex_id) && $admin->complex_id != 0) {

        $query->whereHas('match', function ($q) use ($admin) {
            $q->where('complex_id', $admin->complex_id);
        });

        $tickets = $query->get();

        $complexes = Complex::where('id', $admin->complex_id)->get();

    } else {
        // admin عام → رؤية كل التذاكر
        $tickets = $query->get();
        $complexes = Complex::all();
    }

    return view('admin.tickets.index', compact('tickets', 'complexes'));
}


    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index')
                         ->with('success', 'Ticket supprimé');
    }
}
