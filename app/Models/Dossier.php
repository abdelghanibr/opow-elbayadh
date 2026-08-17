<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dossier extends Model
{
   

    protected $fillable = ['etat','attachments','note_admin','person_id','owner_type','validated_by','validated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }



public function person()
{
    return $this->belongsTo(Person::class);
}

public function club()
{
    return $this->belongsTo(Club::class, 'person_id');
}

public function company()
{
    return $this->belongsTo(Club::class, 'person_id');
}
public function validator()
{
    return $this->belongsTo(User::class, 'validated_by');
}

}
