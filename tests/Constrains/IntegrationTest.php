<?php

namespace Tests\Constrains;

use Tests\TestCase;
use App\Models\Charge;
use App\Models\Currency;
use App\Models\Event;
use App\Models\User;
use App\Models\UserFinantialStatus;
use App\Models\Payment;
use App\Models\PaymentCharge;
use Carbon\Carbon;

class IntegrationTest extends TestCase
{

    /**
     * @return void
     */
    public function testAddCharge()
    {
        $user = $this->createUser();
        $currValue = [
            1 => Currency::findOrFail(1)->value,
            2 => Currency::findOrFail(2)->value,
        ];
        $totalDebtAmount = 0;
        $chargeValues = [];

        // add 6 events with different charges
        for ($i = 0; $i<5; $i++) {
            $amount = rand(50, 200);
            $currId = rand(1, 2);
            $typeId = rand(1,8);
            $chargeValues[$i] = $amount * $currValue[$currId];
            $event = $this->addEvent($amount, $user->id, $currId, $typeId);
            $this->assertEquals($event['amount'], $amount);

            $chargeDebtAmount = $this->getChargeDebt($event['id']);
            $this->assertEquals($event['user_id'], $user->id);
            $this->assertEquals($chargeDebtAmount, $chargeValues[$i]);

            $totalDebtAmount += $chargeDebtAmount;
            $userFinantialDebt = $this->getFinantialDebtAmount($user->id);
            $this->assertEquals($totalDebtAmount, $userFinantialDebt['debt_amount']);
        }

        // make 4 payments
        $paymentAmounts = [
            $chargeValues[0] + $chargeValues[1], // shuold cancel first two charges
            $chargeValues[2] + $chargeValues[3], // shuold cancel second two charges
            $chargeValues[4] + 1, // shuold reject the payment because is 1 over the debt
        ];
        for ($i = 0; $i< 2; $i++) {
            $payment = $this->makePayment($paymentAmounts[$i], $user->id);
            $this->assertEquals($payment['amount'], $paymentAmounts[$i]);
            $this->assertEquals($payment['user_id'], $user->id);
            $userFinantialDebt = $this->getFinantialDebtAmount($user->id);
            $totalDebtAmount -= $payment['amount'];
            $this->assertEquals($totalDebtAmount, $userFinantialDebt['debt_amount']);
        }

        try {
            $payment = $this->makePayment($paymentAmounts[2], $user->id);
        } catch (Exception $e) {
            $this->assertEquals($e->getStatusCode(), 405);
        }

        $payment = $this->makePayment($paymentAmounts[2]-1, $user->id);
        $this->assertEquals($payment['amount'], $paymentAmounts[2]-1);
        $this->assertEquals($payment['user_id'], $user->id);
        $userFinantialDebt = $this->getFinantialDebtAmount($user->id);
        $totalDebtAmount -= $payment['amount'];
        $this->assertEquals($totalDebtAmount, $userFinantialDebt['debt_amount']);
        $this->assertEquals($totalDebtAmount, 0);

        \DB::beginTransaction();
        try {
            $payments = Payment::where('user_id', $user->id)->get();
            foreach($payments as $payment) {
                PaymentCharge::where('payment_id', $payment->id)->delete();
                $payment->delete();
            }
            Charge::where('user_id', $user->id)->delete();
            Event::where('user_id', $user->id)->delete();
            UserFinantialStatus::where('user_id', $user->id)->delete();
            $user->delete();
        } catch (Exception $e) {
            \DB::rollback();
        }
        \DB::commit();
    }

    private function createUser()
    {
        $user = new User();
        $user->name = \Str::random(10);
        $user->email = \Str::random(10).'@gmail.com';
        $user->password = \Hash::make('password');
        $user->save();
        return $user;
    }

    private function addEvent($amount, $userId, $currencyId, $typeId)
    {
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $endpoint = '/api/event';
        $postData = [
            'amount' => $amount,
            'user_id' => $userId,
            'currency_id' => $currencyId,
            'type_id' => $typeId,
        ];
        $response = $this->withHeaders([
            'token' => $token,
        ])->post($endpoint, $postData);
        return json_decode($response->getContent(), true);
    }

    private function getChargeDebt($eventId)
    {
        $endpoint = '/api/charges';
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, [
            'token' => $token,
            'event_id' => $eventId
        ]);
        $respCharges = json_decode($response->getContent(), true);
        return $respCharges[0]['debt_amount'];
    }

    private function getFinantialDebtAmount($userId)
    {
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $endpoint = '/api/users/'.$userId.'/status';
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $userFinantialStatus = json_decode($response->getContent(), true);
        return $userFinantialStatus;
    }

    private function makePayment($amount, $userId)
    {
        $endpoint = '/api/payment';
        $postData = [
            'amount' => $amount,
            'user_id' => $userId,
        ];
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->post($endpoint, $postData);
        return json_decode($response->getContent(), true);
    }
}
