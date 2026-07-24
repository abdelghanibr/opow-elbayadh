<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{

    /* ===================== PUBLIC SHOW ===================== */
    public function show($id)
    {
        $event = Event::where('id', $id)
                      ->where('is_active', true)
                      ->firstOrFail();

        return view('admin.events.show', compact('event'));
    }

    /* ===================== ADMIN INDEX ===================== */
    public function index()
    {
        $events = Event::orderBy('start_date')->get();
        return view('admin.events.index', compact('events'));
    }

    /* ===================== CREATE ===================== */
    public function create()
    {
        return view('admin.events.create');
    }

    /* ===================== STORE ===================== */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        // تحديد المسارات
        if (app()->environment('local')) {
            $storagePath = storage_path('app/public');
            $storageUrl  = '/storage';
        } else {
            $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
            $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
        }

        $data = $request->only([
            'title',
            'description',
            'start_date',
            'end_date',
        ]);

        // ✅ is_active (checkbox)
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
//dd($storagePath);
        // 📸 رفع الصورة
        if ($request->hasFile('image')) {

            if (!is_dir($storagePath.'/events')) {
                mkdir($storagePath.'/events', 0755, true);
            }

            $file     = $request->file('image');
            $fileName = uniqid().'_'.$file->getClientOriginalName();
            $file->move($storagePath.'/events', $fileName);

            $data['image'] = $storageUrl.'/events/'.$fileName;
        }

        Event::create($data);

        return redirect()
            ->route('events.index')
            ->with('success', 'تم إضافة الحدث بنجاح');
    }

    /* ===================== EDIT ===================== */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /* ===================== UPDATE ===================== */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        // تحديد المسارات
        if (app()->environment('local')) {
            $storagePath = storage_path('app/public');
            $storageUrl  = '/storage';
        } else {
            $storagePath = rtrim(env('PUBLIC_STORAGE_PATH'), '/');
            $storageUrl  = rtrim(env('PUBLIC_STORAGE_URL'), '/');
        }

        $data = $request->only([
            'title',
            'description',
            'start_date',
            'end_date',
        ]);

        // ✅ is_active
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // 📸 صورة جديدة؟
        if ($request->hasFile('image')) {

            if (!is_dir($storagePath.'/events')) {
                mkdir($storagePath.'/events', 0755, true);
            }

            // حذف القديمة
            if ($event->image) {
                $oldPath = str_replace($storageUrl, $storagePath, $event->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file     = $request->file('image');
            $fileName = uniqid().'_'.$file->getClientOriginalName();
            $file->move($storagePath.'/events', $fileName);

            $data['image'] = $storageUrl.'/events/'.$fileName;
        }

        $event->update($data);

        return redirect()
            ->route('events.index')
            ->with('success', 'تم تحديث الحدث بنجاح');
    }
    
        public function destroy(Event $event)
    {
        // حذف الصورة إن وجدت
        if ($event->image && file_exists(public_path($event->image))) {
            unlink(public_path($event->image));
        }

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'تم حذف الحدث بنجاح');
    }
}
