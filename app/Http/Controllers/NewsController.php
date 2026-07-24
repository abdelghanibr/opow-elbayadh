<?php

namespace App\Http\Controllers;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderByDesc('created_at')->get();
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

   public function store(Request $request)
{
    $request->validate([
        'title'        => 'required|string|max:255',
        'content'      => 'required|string',
        'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'published_at' => 'nullable|date',
        'is_active'    => 'nullable|boolean',
    ]);

    // 📦 Paths (local / production)
    if (app()->environment('local')) {
        $storagePath = storage_path('app/public');
        $storageUrl  = '/storage';
    } else {
        $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
        $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
    }

    $imagePath = null;

    // 📸 Upload image
    if ($request->hasFile('image')) {

        if (!is_dir($storagePath . '/news')) {
            mkdir($storagePath . '/news', 0755, true);
        }

        $fileName = uniqid() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->move($storagePath . '/news', $fileName);

        $imagePath = $storageUrl . '/news/' . $fileName;
    }

    News::create([
        'title'        => $request->title,
        'content'      => $request->content,
        'image'        => $imagePath,
        'is_active'    => $request->is_active ?? 1,
        'published_at' => $request->published_at,
    ]);

    return redirect()
        ->route('news.index')
        ->with('success', 'تم إضافة الخبر بنجاح ✅');
}


    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

  public function update(Request $request, News $news)
{
    $request->validate([
        'title'        => 'required|string|max:255',
        'content'      => 'required|string',
        'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'published_at' => 'nullable|date',
        'is_active'    => 'nullable|boolean',
    ]);

    // 📦 Paths
    if (app()->environment('local')) {
        $storagePath = storage_path('app/public');
        $storageUrl  = '/storage';
    } else {
        $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
        $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
    }

    $data = $request->only([
        'title',
        'content',
        'published_at',
        
    ]);

    // 📸 New image uploaded
    if ($request->hasFile('image')) {

        if (!is_dir($storagePath . '/news')) {
            mkdir($storagePath . '/news', 0755, true);
        }

        // 🗑️ delete old image
        if ($news->image) {
            $oldPath = str_replace($storageUrl, $storagePath, $news->image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $fileName = uniqid() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->move($storagePath . '/news', $fileName);

        $data['image'] = $storageUrl . '/news/' . $fileName;
    }
    $data['is_active'] = $request->has('is_active') ? 1 : 0;
    $news->update($data);

    return redirect()
        ->route('news.index')
        ->with('success', 'تم تحديث الخبر بنجاح ✅');
}
  public function show($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.show', compact('news'));
    }


public function destroy(News $news)
{
    // 📦 Paths (local / production)
    if (app()->environment('local')) {
        $storagePath = storage_path('app/public');
        $storageUrl  = '/storage';
    } else {
        $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
        $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
    }

    // 🗑️ حذف الصورة إن وجدت
    if ($news->image) {
        $imagePath = str_replace($storageUrl, $storagePath, $news->image);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // 🗑️ حذف الخبر
    $news->delete();

    return redirect()
        ->route('news.index')
        ->with('success', '🗑️ تم حذف الخبر بنجاح');
}


}
