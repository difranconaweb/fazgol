<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    public function bet()
    {
        return $this->belongsTo(Bet::class, 'bet_id', 'id');

    }
    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
}
