<?php

namespace Tests\Endpoints;

use Tests\TestCase;
use App\Models\Event;
use App\Models\Charge;
use App\Models\PaymentCharge;

class ApiEventsTest extends TestCase
{
    /**
     * Endpoint GET events
     *
     * @return void
     */
    public function testApiGetEvents()
    {
        $endpoint = '/api/events';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }

    /**
     * Endpoint GET single Event
     *
     * @return void
     */
    public function testApiGetSingleEvent()
    {
        $endpoint = '/api/events/1';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }

    /**
     * Endpoint POST events
     *
     * @return void
     */
    public function testApiPostEvents()
    {
        $endpoint = '/api/event';
        $response = $this->post($endpoint);
        $response->assertStatus(401);

        $postData = [
            'amount' => 200,
            'user_id' => 1,
            'currency_id' => 1,
            'type_id' => 1,
        ];
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->post($endpoint, $postData);
        $response->assertStatus(200);
        $responseData = json_decode($response->getContent(), true);
        \DB::beginTransaction();
        try {
            $payCharge = PaymentCharge::find($responseData['id']);
            if ($payCharge) {
                PaymentCharge::findOrFail($payCharge->id)->delete();
                $payCharge->payment->delete();
            }
            Charge::where(['event_id' => $responseData['id']])->delete();
            Event::findOrFail($responseData['id'])->delete();
        } catch (Exception $e) {
            \DB::rollback();
        }
        \DB::commit();
    }
}
