<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['amount'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Models\Currency', 'id', 'currency_id');
    }

    public function type()
    {
        return $this->hasOne('App\Models\EventType', 'id', 'type_id');
    }
}
