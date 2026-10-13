<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;
use App\Models\Activity;

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
        $activities = Activity::orderBy('title')->get();
        return view('club.dossier.edit', compact('club', 'activities'));
    }

    // حفظ التعديلات
    public function update(Request $request)
    {
        $club = Club::where('user_id', Auth::id())->firstOrFail();

        $files = array_keys(Club::DOSSIER_FILES);

        // التحقق من الملفات (5 ميغابايت كحد أقصى)
        $validationRules = [];
        foreach ($files as $file) {
            $validationRules[$file] =
                'nullable|file|mimes:' . Club::DOSSIER_MIMES . '|max:' . Club::DOSSIER_MAX_KB;
        }

        $request->validate($validationRules, [
            '*.file'     => '❌ الملف المرفق غير صالح. يُرجى إعادة اختيار الملف.',
            '*.max'      => '❌ حجم الملف كبير جدًا. الحد الأقصى المسموح به هو 2 ميغابايت.',
            '*.uploaded' => '❌ تعذّر رفع الملف على الخادم. الحجم يتجاوز الحد المسموح به (2 ميغابايت). يُرجى ضغط الملف ثم إعادة المحاولة.',
            '*.mimes'    => '❌ صيغة الملف غير مدعومة. الأشكال المسموحة: PDF, JPG, JPEG, PNG.',
        ]);

        $activityId = $request->validate([
            'activity_id' => 'nullable|integer|exists:activities,id',
        ])['activity_id'] ?? null;

        if (app()->environment('local')) {
            $storagePath = storage_path('app/public');
            $storageUrl  = '/storage';
        } else {
            $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
            $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
        }

// المرفقات القديمة — مصفوفة مفهرسة باسم الوثيقة (Club::$attachments)
$attachments = $club->attachments;

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

        // حذف القديم
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
        // مصفوفة عادية فقط: cast 'array' في Club يتولى الترميز، لا json_encode هنا
        'attachments' => $attachments,
        'etat'        => $newEtat,
        'activity_id' => $activityId,
    ]);

        return redirect()
            ->route('club.dossier.index')
            ->with('success', '✅ تم تحديث ملف النادي وإرساله للمراجعة');
    }
}
