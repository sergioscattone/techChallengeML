<?php

namespace Tests\Services;

use Tests\TestCase;
use App\Models\Payment;
use App\Models\Event;
use App\Models\Currency;
use App\Http\Resources\PaymentResource;
use App\Services\EventService;
use App\Services\PaymentService;
use Carbon\Carbon;

class PaymentServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreate()
    {
        // tested in constrains
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testGetAll()
    {
        $from = Carbon::now()->subWeek();
        $to = Carbon::now();
        $paymentService = new PaymentService();
        $paymentsFromService = $paymentService->getAll();
        $payments = PaymentResource::collection(Payment::get());
        $this->assertEquals($paymentsFromService, $payments);

        $paymentsFromService = $paymentService->getAll($from);
        $payments = PaymentResource::collection(Payment::where('created_at', '>=', $from)->get());
        $this->assertEquals($paymentsFromService, $payments);

        $paymentsFromService = $paymentService->getAll(null, $to);
        $payments = PaymentResource::collection(Payment::where('created_at', '<=', $to)->get());
        $this->assertEquals($paymentsFromService, $payments);

        $userId = 1;
        $paymentsFromService = $paymentService->getAll(null, null, $userId);
        $payments = PaymentResource::collection(Payment::where('user_id', $userId)->get());
        $this->assertEquals($paymentsFromService, $payments);

        $paymentsFromService = $paymentService->getAll($from, $to);
        $payments = PaymentResource::collection(
            Payment::where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->get());
        $this->assertEquals($paymentsFromService, $payments);
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $paymentService = new PaymentService();
        $payServ = $paymentService->get(1);
        $payment = new PaymentResource(Payment::findOrFail(1));
        $this->assertEquals($payment, $payServ);
    }
}
