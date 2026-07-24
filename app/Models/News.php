<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'published_at',
        'is_active'
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_active'    => 'boolean'
    ];
}
