<?php
namespace App\Services;

use App\Http\Resources\UserFinantialStatusResource;
use App\Models\UserFinantialStatus;
use App\Models\Charge;
use App\Models\Payment;

class UserFinantialStatusService {


    public function update($userId)
    {
        $debtAmount = Charge
            ::where('user_id', $userId)
            ->where('amount', '>', 0)
            ->sum('amount');

        $creditAmount = Payment
            ::where('user_id', $userId)
            ->sum('amount');

        $UserFinantialStatus = UserFinantialStatus::firstOrNew(['user_id' => $userId]);
        $UserFinantialStatus->user_id = $userId;
        $UserFinantialStatus->balance = $creditAmount - $debtAmount;
        $UserFinantialStatus->save();
    }

    public function getAll($from = null, $to = null)
    {
        return UserFinantialStatusResource::collection(UserFinantialStatus::get());
    }

    public function get($id)
    {
        return new UserFinantialStatusResource(UserFinantialStatus::findOrFail($id));
    }

    public function getByUserId($userId)
    {
        return new UserFinantialStatusResource(UserFinantialStatus::where('user_id', $userId)->get()[0]);
    }
}
