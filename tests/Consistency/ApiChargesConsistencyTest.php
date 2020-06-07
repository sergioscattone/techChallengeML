<?php

namespace Tests\Consistency;

use Tests\TestCase;
use App\Models\Charge;
use App\Http\Resources\ChargeResource;
use Carbon\Carbon;

class ApiChargesConsistencyTest extends TestCase
{
    /**
     * @return void
     */
    public function testApiGetChargesConsistencyNoParams()
    {
        $endpoint = '/api/charges';
        $charges = json_encode(ChargeResource::collection(Charge::get())->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $respCharges = $response->getContent();
        $this->assertEquals($charges, $respCharges);
    }

    /**
     * @return void
     */
    public function testApiGetChargesConsistencyByDate()
    {
        $from = Carbon::now()->subWeek();
        $to = Carbon::now();
        $endpoint = '/api/charges';
        $chargesDB = Charge::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();
        $charges = json_encode(ChargeResource::collection($chargesDB)->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, [
            'token' => $token,
            'from' => $from,
            'to' => $to,
        ]);
        $respCharges = $response->getContent();
        $this->assertEquals($charges, $respCharges);
    }

    /**
     * @return void
     */
    public function testApiGetChargesConsistencyByInvoice()
    {
        $endpoint = '/api/charges';
        $invoiceId = 1;
        $chargesDB = Charge::where('invoice_id', $invoiceId)->get();
        $charges = json_encode(ChargeResource::collection($chargesDB)->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, [
            'token' => $token,
            'invoice_id' => $invoiceId
        ]);
        $respCharges = $response->getContent();
        $this->assertEquals($charges, $respCharges);
    }

    /**
     * @return void
     */
    public function testApiGetChargesConsistencyByUserId()
    {
        $endpoint = '/api/charges';
        $userId = 1;
        $chargesDB = Charge::where('user_id', $userId)->get();
        $charges = json_encode(ChargeResource::collection($chargesDB)->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, [
            'token' => $token,
            'user_id' => $userId
        ]);
        $respCharges = $response->getContent();
        $this->assertEquals($charges, $respCharges);
    }

    /**
     * @return void
     */
    public function testApiGetChargesConsistencyByEventId()
    {
        $endpoint = '/api/charges';
        $eventId = 1;
        $chargesDB = Charge::where('event_id', $eventId)->get();
        $charges = json_encode(ChargeResource::collection($chargesDB)->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, [
            'token' => $token,
            'event_id' => $eventId
        ]);
        $respCharges = $response->getContent();
        $this->assertEquals($charges, $respCharges);
    }

    /**
     * @return void
     */
    public function testApiGetConsistencySingleCharge()
    {
        $chargeId = 1;
        $endpoint = '/api/charges/'.$chargeId;
        $resource = new ChargeResource(Charge::findOrFail($chargeId));
        $charge = json_encode($resource->resolve($resource));
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, ['token' => $token]);
        $respCharge = $response->getContent();
        $this->assertEquals($charge, $respCharge);
    }
}
