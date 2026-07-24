<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeatType extends Model
{
    use HasFactory;

    protected $table = 'seat_types';

    protected $fillable = [
        'name',
        'price'
    ];

    public function seats()
    {
        return $this->hasMany(ComplexSeat::class, 'seat_type_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'seat_type_id');
    }
}
