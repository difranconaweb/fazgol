<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $table = 'leagues';
    public $timestamps = false;


    public function matchies()
    {
        return $this->hasMany(Games::class, 'league_id');
    }
}
