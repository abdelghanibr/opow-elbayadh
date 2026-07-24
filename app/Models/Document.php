<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'dossier_id',
        'type',
        'is_parent_authorization',
        'path',
        'original_name'
    ];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }
}
