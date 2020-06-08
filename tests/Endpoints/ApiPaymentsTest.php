<?php

namespace Tests\Endpoints;

use Tests\TestCase;
use App\Services\EventService;
use App\Models\Payment;
use App\Models\PaymentCharge;
use App\Models\Event;
use App\Models\Charge;

class ApiPaymentsTest extends TestCase
{
    /**
     * Endpoint GET payments
     *
     * @return void
     */
    public function testApiGetPayments()
    {
        $endpoint = '/api/payments';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }

    /**
     * Endpoint GET single payment
     *
     * @return void
     */
    public function testApiGetSinglePayment()
    {
        $endpoint = '/api/payments/1';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }

    /**
     * Endpoint POST payment
     *
     * @return void
     */
    public function testApiPostEvents()
    {
        $endpoint = '/api/payment';
        $response = $this->post($endpoint);
        $response->assertStatus(401);

        $event = (new EventService)->create(123, 1, 1, 1);
        $postData = [
            'amount' => 123,
            'user_id' => 1,
        ];
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->post($endpoint, $postData);
        $response->assertStatus(200);
        $responseData = json_decode($response->getContent(), true);
        \DB::beginTransaction();
        try {
            Charge::where(['event_id' => $event->id])->delete();
            $event->delete();
            $payCharge = PaymentCharge::find($responseData['id']);
            if ($payCharge) {
                PaymentCharge::findOrFail($payCharge->id)->delete();
                $payCharge->payment->delete();
            }
        } catch (Exception $e) {
            \DB::rollback();
        }
        \DB::commit();
    }
}
