<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentCharge extends Model {

    public function charge()
    {
        return $this->hasOne('App\Models\Charge', 'id', 'charge_id');
    }

    public function payment()
    {
        return $this->hasOne('App\Models\Payment', 'id', 'payment_id');
    }
}
