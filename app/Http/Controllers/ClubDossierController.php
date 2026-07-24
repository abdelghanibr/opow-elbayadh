<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;

class ClubDossierController extends Controller
{
    // عرض الدوسيي الخاص بالنادي الحالي
    public function index()
    {
        $club = Club::where('user_id', Auth::id())->firstOrFail();
        return view('club.dossier.index', compact('club'));
    }

    // صفحة التعديل
    public function edit()
    {
        $club = Club::where('user_id', Auth::id())->firstOrFail();
        return view('club.dossier.edit', compact('club'));
    }

    // حفظ التعديلات
    public function update(Request $request)
    {
        $club = Club::where('user_id', Auth::id())->firstOrFail();

        $attachments = json_decode($club->attachments, true) ?? [];

        // رفع الملفات
        $files = [
            'agrement',
            'statut',
            'bureau_members',
            'coaches_certificates',
            'federation_affiliation',
            'insurance_certificate',
            'rules_book',
            'minutes_meeting',
            'exploitation_request'
        ];
if (app()->environment('local')) {
    $storagePath = storage_path('app/public');
    $storageUrl  = '/storage';
} else {
    $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
    $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
}

// المرفقات القديمة
$oldAttachments = json_decode($club->attachments ?? '{}', true);
$attachments = $oldAttachments ?? [];

foreach ($files as $file) {

    if ($request->hasFile($file)) {

        $uploadedFile = $request->file($file);

        // تنظيف الاسم
        $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $extension    = $uploadedFile->getClientOriginalExtension();
        $cleanName    = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);

        $fileName = uniqid() . '_' . $cleanName . '.' . $extension;

        // إنشاء المجلد إن لم يوجد
        if (!is_dir($storagePath . '/clubs/dossiers')) {
            mkdir($storagePath . '/clubs/dossiers', 0755, true);
        }

        // حذف القديم (اختياري)
        if (!empty($attachments[$file])) {
            $oldFilePath = str_replace($storageUrl, $storagePath, $attachments[$file]);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // حفظ الجديد
        $uploadedFile->move(
            $storagePath . '/clubs/dossiers',
            $fileName
        );

        $attachments[$file] = $storageUrl . '/clubs/dossiers/' . $fileName;
    }
}

// تحديث
$newEtat = $club->etat === 'approved'
        ? 'approved'   // ✅ نحتفظ بالمصادقة
        : 'pending';    // 🔄 يعاد للدراسة فقط إذا لم يكن مصادقًا

    /* ===== Update club ===== */
    $club->update([
        'attachments' => json_encode($attachments),
        'etat'        => $newEtat,
    ]);

        
        return redirect()
            ->route('club.dossier.index')
            ->with('success', '✅ تم تحديث ملف النادي وإرساله للمراجعة');
    }
}
