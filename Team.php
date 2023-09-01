<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    public function odd()
    {
        return $this->belongsTo(Odd::class, 'odd_id', 'id');
    }
}
