<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
  protected $fillable = [
        'activity_id',
        'age_category_id',
        'sexe',
        'type_client',
        'pricing_type',
        'duration_unit',
        'duration_value',
        'sessions_per_week',
        'price',
        'active',
        'valid_from',
        'valid_to',
        'name'
    ];
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }


    public function ageCategory()
    {
        return $this->belongsTo(AgeCategory::class, 'age_category_id');
    }
    public function reservations()
{
    return $this->hasMany(Reservation::class, 'pricing_plan_id');
}

}
