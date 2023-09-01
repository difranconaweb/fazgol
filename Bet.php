<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $table = 'bets';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function match()
    {
        return $this->belongsTo(Games::class, 'match_id', 'id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'bet_id', 'id');
    }
}
