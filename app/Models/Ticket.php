<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'match_id',
        'seat_type_id',
        'buyer_name',
        'buyer_phone',
        'qr_code',
        'status',
        'payment_id',
    ];

    public function match()
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function seatType()
    {
        return $this->belongsTo(SeatType::class, 'seat_type_id');
    }
}
