<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityCategory extends Model
{
    use HasFactory;

    protected $table = 'activity_categories';

    protected $fillable = [
        'name',
        'icon',
        'color',
    ];
}
