<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    protected $table = 'matchs';
    public $timestamps = false;


    public function leagues()
    {
        return $this->belongsTo(League::class, 'league_id', 'id');
    }

    public function bet()
    {
        return $this->hasMany(Bet::class, 'match_id', 'id');
    }

    public function team_one()
    {
        return $this->hasOne(Team::class, 'id', 'team1_id');
    }

    public function team_two()
    {
        return $this->hasOne(Team::class, 'id', 'team2_id');
    }
}
