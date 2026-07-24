<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table = 'devices';

    /**
     * الحقول القابلة للإدخال (Mass Assignment)
     */
    protected $fillable = [
        'name',
        'model',
        'ip',
        'port',
        'location',
        'is_active',
        'last_sync',
    ];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_sync' => 'datetime',
    ];

    /**
     * علاقة: الجهاز لديه عدة مستخدمين ZK
     */
    public function zkUsers()
    {
        return $this->hasMany(ZkUser::class);
    }

    /**
     * علاقة: الجهاز لديه عدة سجلات حضور
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
