<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Activity;
use App\Models\ComplexActivity;
use App\Models\Reservation;
use App\Models\ComplexSeat;
use App\Models\MatchModel; // تأكد من الاسم الصحيح لموديل المباريات

class Complex extends Model
{
    protected $fillable = [
        'nom',
        'type',
        'adresse',
        'phone',
        'capacite_mi',
        'capacite_ma',
        'image','type','user_id'
    ];

    /**
     * 🔵 علاقة المركب مع الأنشطة عبر جدول pivot: complex_activity
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'complex_activity')
                    ->withPivot(['capacity', 'season_id'])
                    ->withTimestamps();
    }

    /**
     * 🟢 علاقة المركب مع جدول complex_activity مباشرة
     */
    public function complexActivities()
    {
        return $this->hasMany(ComplexActivity::class, 'complex_id');
    }

    /**
     * 🟡 علاقة المركب مع الحجوزات عبر complex_activity
     * تعمل فقط إذا كان جدول reservations يحتوي على complex_activity_id
     */
    public function reservations()
    {
        return $this->hasManyThrough(
            Reservation::class,        // النموذج النهائي
            ComplexActivity::class,    // النموذج الوسيط
            'complex_id',              // FK في complex_activity يشير للمركب
            'complex_activity_id',     // FK في reservations يشير لـ complex_activity
            'id',                      // PK في complexes
            'id'                       // PK في complex_activity
        );
    }

    /**
     * 🪑 علاقة المركب مع المقاعد (Seat distribution)
     */
    public function seats()
    {
        return $this->hasMany(ComplexSeat::class, 'complex_id');
    }

    /**
     * ⚽ علاقة المركب مع المباريات
     * تأكد من الاسم الصحيح لموديل Match
     */
    public function matches()
    {
        return $this->hasMany(MatchModel::class, 'complex_id');
    }
}
