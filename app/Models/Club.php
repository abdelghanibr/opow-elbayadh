<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = [
        'user_id',
        'nom',
        'numero_agrement',
        'date_expiration',
        'attachments',
        'entity_type' ,
        'status',
        'validatio',
        'date_validation',
        'validated_by','etat'  ,'validated_at' 


 
    ];
public function validator()
{
    return $this->belongsTo(User::class, 'validated_by');
}
    protected $casts = [
        'attachments' => 'array', // <-- مهم جدًا
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // أعضاء النادي
    public function members()
    {
        return $this->hasMany(Person::class);
    }
}
