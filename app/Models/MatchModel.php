<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchModel extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'complex_id',
        'team_home_id',
        'team_away_id',
        'match_date',
        'match_time',
        'competition',
        'status'
    ];

    public function complex()
    {
        return $this->belongsTo(Complex::class, 'complex_id');
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'team_home_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'team_away_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'match_id');
    }
}
