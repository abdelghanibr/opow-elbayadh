<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; 
class ClubController extends Controller
{
   /* public function index()
    {
        $clubs = Club::with('user')->orderByDesc('id')->get();
        return view('admin.clubs.index', compact('clubs'));
    }*/
    
    public function index()
{
    $admin = auth()->user(); // المستخدم المتصل

    // إذا كان للمدير مجمع معيّن → اجلب فقط الأندية التابعة له
    if (!empty($admin->complex_id) && $admin->complex_id != 0) {
        $clubs = Club::with('user')
            ->whereHas('user', function ($q) use ($admin) {
                $q->where('complex_id', $admin->complex_id);
            })
            ->orderByDesc('id')
            ->get();
    } else {
        // إذا لا يوجد مجمع → اظهر جميع الأندية
        $clubs = Club::with('user')
            ->orderByDesc('id')
            ->get();
    }

    return view('admin.clubs.index', compact('clubs'));
}


    public function approve($id)
    { 
        $club = Club::findOrFail($id);

//dd($club) ;
        $club->update([
            'etat' => 'approved',
            'validated_by' => Auth::id(),
           'validated_at' => now(),
        ]);

        return back()->with('success','تم قبول النادي ✔');
    }

    public function reject($id)
    {
        $club = Club::findOrFail($id);
        $club->update([
            'etat' => 'rejected',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('error','تم رفض النادي ❌');
    }

    public function update(Request $request)
{
    $club = Auth::user()->club;

    $attachments = $club->attachments ?? [];

    $fields = [
        'club_accreditation',      // اعتماد النادي
        'club_statute',            // القانون الأساسي
        'management_list',         // قائمة أعضاء المكتب
        'coaches_certificates',    // شهادات المدربين
        'federation_membership',   // شهادة الانخراط
        'insurance_certificate',   // شهادة التأمين
        'terms_register',          // دفتر الشروط
        'agreement_register',      // دفتر الاتفاقية
        'exploitation_request',    // طلب الاستغلال
    ];

    foreach ($fields as $field) {
        if ($request->hasFile($field)) {

            $path = $request->file($field)
                ->store("clubs/{$club->id}", 'public');

            $attachments[$field] = Storage::url($path);
        }
    }

    $club->update([
        'attachments' => $attachments,
    ]);

    return back()->with('success', '✅ تم تحديث ملف النادي بنجاح');
}

public function updateNote(Request $request, Dossier $dossier)
{
    $request->validate([
        'note_admin' => 'required|string|max:1000',
    ]);

    $club->update([
        'note_admin' => $request->note_admin,
    ]);

    return back()->with('success', '✔ تم حفظ الملاحظة بنجاح');
}

public function note(Request $request, $id)
    {
        $club = Club::findOrFail($id);
        $club->note_admin = $request->input('note_admin');
        $club->save();

        return redirect()->route('admin.clubs.index')->with('success', 'Note ajoutée avec succès!');
    }

}
