<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use App\Models\Person;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\Complex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $clubs = Club::with(['user', 'user.complex'])
            ->where('entity_type', 'club')
            ->whereHas('user', function ($q) use ($admin) {
                $q->where('complex_id', $admin->complex_id);
            })
            ->orderByDesc('id')
            ->get();
        $complexes = Complex::where('id', $admin->complex_id)->get();
    } else {
        // إذا لا يوجد مجمع → اظهر جميع الأندية
        $clubs = Club::with(['user', 'user.complex'])
            ->where('entity_type', 'club')
            ->orderByDesc('id')
            ->get();
        $complexes = Complex::orderBy('nom')->get();
    }

    return view('admin.clubs.index', compact('clubs', 'complexes'));
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

    /**
     * 🗑️ حذف النادي وحساب تسجيل الدخول الخاص به (فقط إذا لم يكن مقبولاً)
     */
    public function destroy($id)
    {
        $club = Club::findOrFail($id);

        $userId = $club->user_id;

        DB::transaction(function () use ($club, $userId) {
            // Réservations du compte ou de ses membres
            $memberIds = Person::where('user_id', $userId)->pluck('id');

            $resQuery = Reservation::query();
            if ($memberIds->isNotEmpty()) {
                $resQuery->where(function ($q) use ($userId, $memberIds) {
                    $q->where('user_id', $userId)->orWhereIn('person_id', $memberIds);
                });
            } else {
                $resQuery->where('user_id', $userId);
            }
            $resQuery->delete();

            // updated_by orphelin
            Reservation::where('updated_by', $userId)->update(['updated_by' => null]);

            // Schedules liés au compte
            Schedule::where('user_id', $userId)->update(['user_id' => null]);

            // Membres + leurs dossiers
            foreach (Person::where('user_id', $userId)->get() as $member) {
                \App\Models\Dossier::where('person_id', $member->id)->delete();
                $member->delete();
            }

            // Le club
            $club->delete();

            // Le login (compte utilisateur)
            User::where('id', $userId)->delete();
        });

        return back()->with('success', '🗑️ تم حذف النادي وحساب تسجيل الدخول الخاص به بنجاح.');
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

    public function companies()
    {
        $admin = auth()->user();

        if (!empty($admin->complex_id) && $admin->complex_id != 0) {
            $companies = Club::with(['user', 'user.complex'])
                ->where('entity_type', 'company')
                ->whereHas('user', function ($q) use ($admin) {
                    $q->where('complex_id', $admin->complex_id);
                })
                ->orderByDesc('id')
                ->get();
            $complexes = Complex::where('id', $admin->complex_id)->get();
        } else {
            $companies = Club::with(['user', 'user.complex'])
                ->where('entity_type', 'company')
                ->orderByDesc('id')
                ->get();
            $complexes = Complex::orderBy('nom')->get();
        }

        return view('admin.companies.index', compact('companies', 'complexes'));
    }

    public function approveCompany($id)
    {
        $company = Club::findOrFail($id);
        $company->update([
            'etat' => 'approved',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'تم قبول المؤسسة ✔');
    }

    public function rejectCompany($id)
    {
        $company = Club::findOrFail($id);
        $company->update([
            'etat' => 'rejected',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('error', 'تم رفض المؤسسة ❌');
    }

    public function destroyCompany($id)
    {
        $company = Club::findOrFail($id);
        $userId = $company->user_id;

        DB::transaction(function () use ($company, $userId) {
            $memberIds = Person::where('user_id', $userId)->pluck('id');

            $resQuery = Reservation::query();
            if ($memberIds->isNotEmpty()) {
                $resQuery->where(function ($q) use ($userId, $memberIds) {
                    $q->where('user_id', $userId)->orWhereIn('person_id', $memberIds);
                });
            } else {
                $resQuery->where('user_id', $userId);
            }
            $resQuery->delete();

            Reservation::where('updated_by', $userId)->update(['updated_by' => null]);
            Schedule::where('user_id', $userId)->update(['user_id' => null]);

            foreach (Person::where('user_id', $userId)->get() as $member) {
                \App\Models\Dossier::where('person_id', $member->id)->delete();
                $member->delete();
            }

            $company->delete();
            User::where('id', $userId)->delete();
        });

        return back()->with('success', '🗑️ تم حذف المؤسسة وحساب تسجيل الدخول الخاص به بنجاح.');
    }

    public function noteCompany(Request $request, $id)
    {
        $company = Club::findOrFail($id);
        $company->note_admin = $request->input('note_admin');
        $company->save();

        return redirect()->route('admin.companies.index')->with('success', 'Note ajoutée avec succès!');
    }

}
