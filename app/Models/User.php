<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'type','complex_id','nin' ,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
   public function complex()
    {
        return $this->belongsTo(Complex::class, 'complex_id');
    }
    // علاقة لكل نوع مستخدم
    public function person()
    {
        return $this->hasOne(Person::class);
    }

    public function club()
    {
        return $this->hasOne(Club::class);
    }

    public function dossier()
    {
        return $this->hasOne(Dossier::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    


public function latestReservation()
{
    return $this->hasOne(Reservation::class)->ofMany('start_date', 'max');
}
}
