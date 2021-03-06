<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['amount'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
