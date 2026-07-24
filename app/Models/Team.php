<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = [
        'name',
        'logo'
    ];

    public function homeMatches()
    {
        return $this->hasMany(MatchModel::class, 'team_home_id');
    }

    public function awayMatches()
    {
        return $this->hasMany(MatchModel::class, 'team_away_id');
    }
}
