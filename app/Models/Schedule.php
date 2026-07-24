<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'complex_activity_id',
        'age_category_id',
        'groupe',
        'sex',
        'nbr',
        'time_slots',
        'type_prix',
        'price',
        'user_id', 'type_season','date_debut','date_fin'  ,'active' ,     // club / entreprise (nullable)
    ];

    /**
     * Cast JSON → array
     */
    protected $casts = [
        'time_slots' => 'array',
        'date_debut' => 'date',
    'date_fin'   => 'date',
    ];

    /**
     * Relations
     */

    // علاقة مع complex_activities
    public function complexActivity()
    {
        return $this->belongsTo(ComplexActivity::class);
    }

    // الوصول إلى المركب مباشرة schedule->complex
    public function complex()
    {
        return $this->complexActivity->complex();
    }

    // الوصول إلى النشاط مباشرة schedule->activity
    public function activity()
    {
        return $this->complexActivity->activity();
    }

    // علاقة الفئة العمرية
    public function ageCategory()
    {
        return $this->belongsTo(AgeCategory::class);
    }

    // المستخدم (club / entreprise) إن وجد
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
