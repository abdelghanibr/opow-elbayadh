<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMessage;
use App\Services\ImpersonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Messages / notifications de l'administration vers les comptes clients (super admin). */
class MessageController extends Controller
{
    private function guard(): void
    {
        abort_unless(ImpersonationService::isSuperAdmin(Auth::user()), 403, 'غير مصرح لك');
    }

    public function index(Request $request)
    {
        $this->guard();

        $ready = Schema::hasTable('user_messages');
        $q = trim((string) $request->query('q', ''));

        $messages = !$ready ? collect() : UserMessage::query()
            ->leftJoin('users', 'users.id', '=', 'user_messages.user_id')
            ->select('user_messages.*', 'users.email as user_email', 'users.name as user_name')
            ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x->where('users.email', 'like', "%$q%")
                ->orWhere('users.name', 'like', "%$q%")->orWhere('user_messages.title', 'like', "%$q%")))
            ->orderByDesc('user_messages.id')->limit(200)->get();

        return view('admin.messages.index', compact('messages', 'ready', 'q'));
    }

    public function store(Request $request)
    {
        $this->guard();
        abort_unless(Schema::hasTable('user_messages'), 500, 'جدول الرسائل غير موجود: نفّذ ملف SQL أولاً.');

        $data = $request->validate([
            'emails'         => 'required|string|max:5000',
            'title'          => 'required|string|max:255',
            'body'           => 'required|string|max:5000',
            'reservation_id' => 'nullable|integer|min:1',
        ], [
            'emails.required' => 'أدخل بريدًا إلكترونيًا واحدًا على الأقل.',
            'title.required'  => 'العنوان مطلوب.',
            'body.required'   => 'نص الرسالة مطلوب.',
        ]);

        $emails = collect(preg_split('/[\s,;]+/', $data['emails'], -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($e) => mb_strtolower(trim($e)))->unique()->values();

        $users = User::whereIn(DB::raw('LOWER(email)'), $emails->all())->get(['id', 'email'])
            ->keyBy(fn ($u) => mb_strtolower($u->email));

        $missing = $emails->reject(fn ($e) => $users->has($e))->values();

        DB::transaction(function () use ($users, $data) {
            foreach ($users as $u) {
                UserMessage::create([
                    'user_id'        => $u->id,
                    'reservation_id' => $data['reservation_id'] ?? null,
                    'title'          => $data['title'],
                    'body'           => $data['body'],
                    'sent_by'        => 'الإدارة',
                ]);
            }
        });

        $msg = 'تم إرسال الرسالة إلى ' . $users->count() . ' حساب.';
        if ($missing->isNotEmpty()) {
            $msg .= ' بريد غير موجود: ' . $missing->implode('، ');
        }

        return redirect()->route('admin.messages.index')->with($users->isEmpty() ? 'error' : 'success', $msg);
    }

    public function destroy(int $message)
    {
        $this->guard();
        UserMessage::where('id', $message)->delete();

        return back()->with('success', 'تم حذف الرسالة.');
    }
}
