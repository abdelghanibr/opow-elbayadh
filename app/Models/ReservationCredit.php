<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationCredit extends Model
{
    protected $table = 'reservation_credits';

    protected $fillable = [
        'reservation_id',
        'user_id',
        'complex_activity_id',
        'closure_date',
        'credited_amount',
        'status',
        'used_in_reservation_id',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
