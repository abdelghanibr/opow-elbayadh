<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\BackupService;
use Illuminate\Http\Request;

/** Journal d'audit + sauvegardes — réservé à l'administrateur général. */
class AuditController extends Controller
{
    private function guard(): void
    {
        $u = auth()->user();
        abort_unless($u && $u->type === 'admin' && empty($u->complex_id), 403, 'غير مصرح لك');
    }

    public function index(Request $r)
    {
        $this->guard();

        $q = AuditLog::query()->orderByDesc('id');
        if ($r->filled('user'))   { $q->where(fn ($w) => $w->where('user_name', 'like', '%' . $r->user . '%')->orWhere('user_id', $r->user)); }
        if ($r->filled('event'))  { $q->where('event', $r->event); }
        if ($r->filled('model'))  { $q->where('auditable_type', 'like', '%' . $r->model . '%'); }
        if ($r->filled('record')) { $q->where('auditable_id', $r->record); }
        if ($r->filled('from'))   { $q->where('created_at', '>=', $r->from . ' 00:00:00'); }
        if ($r->filled('to'))     { $q->where('created_at', '<=', $r->to . ' 23:59:59'); }
        if (!$r->boolean('with_requests')) { $q->where('event', '!=', 'request'); }

        return view('admin.audit.index', [
            'logs'   => $q->paginate(50)->withQueryString(),
            'events' => AuditLog::select('event')->distinct()->orderBy('event')->pluck('event'),
        ]);
    }

    public function backups()
    {
        $this->guard();
        return view('admin.audit.backups', ['files' => BackupService::list(), 'days' => BackupService::DAYS]);
    }

    public function backupRun()
    {
        $this->guard();
        $res = BackupService::run('manual');
        return back()->with($res['ok'] ? 'success' : 'error',
            $res['ok'] ? 'تم إنشاء النسخة الاحتياطية: ' . $res['file'] : 'فشل النسخ الاحتياطي: ' . $res['error']);
    }

    public function backupDownload(string $name)
    {
        $this->guard();
        abort_unless(preg_match('/^db_[0-9]{8}_[0-9]{6}_[a-z0-9_-]+\.sql\.gz$/i', $name), 404);
        $path = BackupService::dir() . DIRECTORY_SEPARATOR . $name;
        abort_unless(is_file($path), 404);
        return response()->download($path);
    }
}
