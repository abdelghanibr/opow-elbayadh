<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;

class EntrepriseDossierController extends Controller
{
    /**
     * 📁 عرض ملف المؤسسة (من جدول clubs)
     */
    public function index()
    {
        $club = Club::where('user_id', auth()->id())->first();

        if (!$club) {
            abort(403, 'لا يوجد ملف مرتبط بهذا الحساب');
        }

        return view('entreprise.dossier.index', [
            'enterprise' => $club, // ⚠️ نمرره كـ enterprise للـ view
        ]);
    }

    /**
     * ✏️ صفحة تعديل ملف المؤسسة
     */
    public function edit()
    {
        $club = Club::where('user_id', auth()->id())->first();

        if (!$club) {
            abort(403, 'لا يوجد ملف مرتبط بهذا الحساب');
        }

        return view('entreprise.dossier.edit', [
            'enterprise' => $club, // نفس المتغير المستعمل في الـ view
        ]);
    }

    /**
     * 💾 حفظ / تحديث ملف المؤسسة
     */
public function update(Request $request)
{
    $club = \App\Models\Club::where('user_id', auth()->id())->first();

    if (!$club) {
        abort(403, 'لا يوجد ملف مؤسسة مرتبط بهذا الحساب');
    }

    // المرفقات الحالية — مصفوفة مفهرسة (Club::$attachments)
    $attachments = $club->attachments;

    foreach ($request->files as $key => $file) {

        if (!$file) {
            continue;
        }

        /* ===============================
           تحديد المسار حسب البيئة
        =============================== */
        if (app()->environment('local')) {
            $storagePath = storage_path('app/public');
            $storageUrl  = '/storage';
        } else {
            $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
            $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
        }

        $directory = $storagePath . '/uploads/entreprise';

        // إنشاء المجلد إن لم يكن موجودًا
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // اسم فريد
        $filename = $key . '_' . time() . '.' . $file->getClientOriginalExtension();

        // نقل الملف
        $file->move($directory, $filename);

        // المسار النهائي المخزن في DB
        $attachments[$key] = $storageUrl . '/uploads/entreprise/' . $filename;
    }

    // تحديث الدوسيي
    $club->update([
        'attachments' => $attachments,
        'etat'        => 'pending',
    ]);

    return redirect()
        ->route('entreprise.dossier.index')
        ->with('success', '✅ تم تحديث ملف المؤسسة وإرساله للمراجعة');
}

}
