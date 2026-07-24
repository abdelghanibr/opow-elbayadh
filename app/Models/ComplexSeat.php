<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplexSeat extends Model
{
    use HasFactory;

    protected $table = 'complex_seats';

    protected $fillable = [
        'complex_id',
        'seat_type_id',
        'total_seats',
        'available_seats'
    ];
    public $timestamps = false;

    public function complex()
    {
        return $this->belongsTo(Complex::class, 'complex_id');
    }

    public function seatType()
    {
        return $this->belongsTo(SeatType::class, 'seat_type_id');
    }
}
