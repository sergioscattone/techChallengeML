<?php
namespace App\Services;

use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\UserFinantialStatusService;
use App\Models\Payment;
use App\Models\Charge;
use App\Models\UserFinantialStatus;
use App\Http\Resources\PaymentResource;

class PaymentService {
    public function create($amount, $userId) {
        if (empty($amount) || !is_numeric($amount) || empty($userId)) {
            throw new HttpException(400, 'missing parameters: you should have: amount, user_id. amount must be > 0');
        }
        $debtAmount = UserFinantialStatus::where('user_id', $userId)->get()[0]['debt'];
        if ($debtAmount < $amount) {
            throw new HttpException(405, 'The payment ('.$amount.') must be less than the total debt ('.$debtAmount.')');
        }
        $chargeModel = new Charge();
        $debtCharges = $chargeModel
            ->where('user_id', $userId)
            ->where('debt_amount', '>', 0)
            ->orderBy('id', 'ASC')
            ->get();
        \DB::beginTransaction();
        try {
            $paymentModel = app('App\Models\Payment');
            $paymentModel->amount = $amount;
            $paymentModel->user_id = $userId;
            $paymentModel->save();
            $remAmount = $amount;
            foreach($debtCharges as $debtCharge) {
                $paymentChargeModel = app('App\Models\PaymentCharge');
                $paymentChargeModel->payment_id = $paymentModel->id;
                $paymentChargeModel->charge_id = $debtCharge->id;
                $paymentChargeModel->save();
                if ($debtCharge->debt_amount >= $remAmount) {
                    $debtCharge->debt_amount = $debtCharge->debt_amount - $remAmount;
                    $debtCharge->save();
                    break;
                } else {
                    $remAmount -= $debtCharge->debt_amount;
                    $debtCharge->debt_amount = 0;
                    $debtCharge->save();
                }
            }
            (new UserFinantialStatusService)->update($userId);
        } catch (Exception $e) {
            \DB::rollback();
            throw new HttpException(500, 'There was an error processing your payment');
        }
        \DB::commit();
        return new PaymentResource($paymentModel);
    }

    public function createFromBulk($amount, $userId) {
        try {
            $this->create($amount, $userId);
        } catch (HttpException $e) {
            // overpayment dont stop the bulk
            if ($e->getStatusCode() != 405) {
                throw $e;
            }
        }
    }

    public function getAll($from = null, $to = null, $userId = null) {
        $paymentModel = new Payment();
        if ($from) {
            $paymentModel = $paymentModel->where('created_at', '>=', $from);
        }
        if ($to) {
            $paymentModel = $paymentModel->where('created_at', '<=', $to);
        }
        if ($userId) {
            $paymentModel = $paymentModel->where('user_id', $userId);
        }
        return PaymentResource::collection($paymentModel->get());
    }

    public function get($id) {
        return new PaymentResource(Payment::findOrFail($id));
    }
}
