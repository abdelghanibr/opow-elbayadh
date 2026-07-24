<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgeCategory extends Model
{
    protected $fillable = ['name', 'min_age', 'max_age'];

    public function persons()
    {
        return $this->hasMany(Person::class);
    }
}
