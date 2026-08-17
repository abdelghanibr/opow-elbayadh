<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonAssurance extends Model
{
    protected $fillable = [
        'person_id',
        'reservation_id',
        'start_date',
        'end_date',
        'status',
        'operation_type',
        'source',
        'printed_at',
        'created_by',
        'printed_by',
        'assured_at',
        'cancelled_at',
        'cancelled_by',
        'note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'printed_at' => 'datetime',
        'assured_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function printedBy()
    {
        return $this->belongsTo(User::class, 'printed_by');
    }
}
