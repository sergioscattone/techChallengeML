<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFinantialStatus extends Model
{
    protected $fillable = ['balance'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
