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
        $balance = 0;
        $overPayment = 800;
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

            $balance -= $chargeDebtAmount;
            $userFinantialBalance = $this->getFinantialBalance($user->id);
            $this->assertEquals($balance, $userFinantialBalance['balance']);
        }

        $this->assertEquals(-$balance, array_sum($chargeValues));

        // make 4 payments
        $paymentAmounts = [
            $chargeValues[0] + $chargeValues[1], // should cancel first two charges
            $chargeValues[2] + $chargeValues[3], // should cancel second two charges
            $chargeValues[4] + $overPayment, // should reserve 800 for next charge/s
        ];

        for ($i = 0; $i< 2; $i++) {
            $payment = $this->makePayment($paymentAmounts[$i], $user->id);
            $this->assertEquals($payment['amount'], $paymentAmounts[$i]);
            $this->assertEquals($payment['user_id'], $user->id);
            $userFinantialBalance = $this->getFinantialBalance($user->id);
            $balance += $payment['amount'];
            $this->assertEquals($balance, $userFinantialBalance['balance']);
        }

        // overpayment as credit 800
        $payment = $this->makePayment($paymentAmounts[2], $user->id);
        $this->assertEquals($payment['amount'], $paymentAmounts[2]);
        $this->assertEquals($payment['user_id'], $user->id);
        $userFinantialBalance = $this->getFinantialBalance($user->id);
        $this->assertEquals($userFinantialBalance['balance'], $overPayment);
        $balance += $payment['amount'];
        $this->assertEquals($balance, $overPayment);

        // create 2 new charges, one for the half of the overpayment
        // and the other one for total, should create 3 chartes
        // last one with 400 of debt_amount for pay
        for($i=2; $i>0; $i--) {
            $event = $this->addEvent($overPayment / $i, $user->id, 1, 1);
            $this->assertEquals($event['amount'], $overPayment / $i);
            $balance -= $event['amount'];
            $userFinantialBalance = $this->getFinantialBalance($user->id);
            $this->assertEquals($balance, $userFinantialBalance['balance']);
        }

        // debt should be -400
        $charges = $this->getChargeDebtByUserID($user->id);
        $totalDebt = 0;
        foreach($charges as $charge) {
            $totalDebt -= $charge['debt_amount'];
        }
        $this->assertEquals($totalDebt, $balance);

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

    private function getFinantialBalance($userId)
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

    private function getChargeDebtByUserID($userId)
    {

        $endpoint = '/api/charges';
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, [
            'token' => $token,
            'user_id' => $userId
        ]);
        return json_decode($response->getContent(), true);
    }
}
