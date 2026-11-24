<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Message / notification envoyé par l'administration à un client (affiché dans le compte). */
class UserMessage extends Model
{
    protected $fillable = ['user_id', 'reservation_id', 'code', 'title', 'body', 'sent_by', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    /** Nombre de messages non lus ; 0 si la table n'existe pas encore (avant exécution du SQL). */
    public static function unreadCount(?int $userId): int
    {
        if (!$userId) {
            return 0;
        }
        try {
            return (int) static::where('user_id', $userId)->whereNull('read_at')->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Messages du client, du plus récent au plus ancien (vide si la table n'existe pas encore).
     * $all = true : historique complet (zone « رسائل الإدارة » du profil).
     * $all = false : notifications — un message lu disparaît 24 h après sa lecture.
     */
    public static function latestFor(?int $userId, int $limit = 20, bool $all = false)
    {
        if (!$userId) {
            return collect();
        }
        try {
            // un message lu reste affiché 24 h après sa lecture, puis disparaît du compte du client
            return static::where('user_id', $userId)
                ->when(!$all, fn ($q) => $q->where(fn ($w) => $w->whereNull('read_at')->orWhere('read_at', '>=', now()->subHours(24))))
                ->orderByDesc('created_at')->orderByDesc('id')->limit($limit)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
