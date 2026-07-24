<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplexActivity extends Model
{
    protected $table = 'complex_activity';

    protected $fillable = [
        'complex_id',
        'activity_id',
        'capacity',
       
    ];

    public function complex()
    {
        return $this->belongsTo(Complex::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
