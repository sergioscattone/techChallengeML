<?php

namespace Tests\Consistency;

use Tests\TestCase;
use App\Models\Payment;
use App\Http\Resources\PaymentResource;
use Carbon\Carbon;

class ApiPaymentsConsistencyTest extends TestCase
{
    /**
     * @return void
     */
    public function testApiGetPaymentsConsistencyNoParams()
    {
        $endpoint = '/api/payments';
        $payments = json_encode(PaymentResource::collection(Payment::get())->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $respPayments = $response->getContent();
        $this->assertEquals($payments, $respPayments);
    }

    /**
     * @return void
     */
    public function testApiGetPaymentsConsistencyByDate()
    {
        $from = Carbon::now()->subWeek();
        $to = Carbon::now();
        $endpoint = '/api/payments';
        $paymentsDB = Payment::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();
        $payments = json_encode(PaymentResource::collection($paymentsDB)->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, [
            'token' => $token,
            'from' => $from,
            'to' => $to,
        ]);
        $respPayments = $response->getContent();
        $this->assertEquals($payments, $respPayments);
    }

    /**
     * @return void
     */
    public function testApiGetConsistencySinglePayments()
    {
        $paymentsId = 1;
        $endpoint = '/api/payments/'.$paymentsId;
        $resource = new PaymentResource(Payment::findOrFail($paymentsId));
        $payment = json_encode($resource->resolve($resource));
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, ['token' => $token]);
        $respPayment = $response->getContent();
        $this->assertEquals($payment, $respPayment);
    }
}
