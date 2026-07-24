<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'title', 'description', 'color', 'is_active', 'activity_category_id', 'icon','user_id'
    ];

    public function complexes()
    {
        return $this->belongsToMany(Complex::class, 'complex_activity')
                    ->withPivot(['capacity','season_id'])
                    ->withTimestamps();
    }

    public function activityCategory()
{
    return $this->belongsTo(ActivityCategory::class);
}
public function complexActivities()
{
    return $this->hasMany(ComplexActivity::class);
}

}
