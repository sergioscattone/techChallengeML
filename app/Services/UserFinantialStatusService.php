<?php
namespace App\Services;

use App\Http\Resources\UserFinantialStatusResource;
use App\Models\UserFinantialStatus;
use App\Models\Charge;

class UserFinantialStatusService {


    public function update($userId)
    {
        $ChargeModel = new Charge();
        $debtAmount = $ChargeModel
            ->where('user_id', $userId)
            ->where('debt_amount', '>', 0)
            ->sum('debt_amount');

        $UserFinantialStatus = UserFinantialStatus::firstOrNew(['user_id' => $userId]);
        $UserFinantialStatus->user_id = $userId;
        $UserFinantialStatus->debt = $debtAmount;
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
