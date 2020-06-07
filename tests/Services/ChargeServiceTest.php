<?php

namespace Tests\Services;

use Tests\TestCase;
use App\Models\Charge;
use App\Models\Event;
use App\Models\Currency;
use App\Http\Resources\ChargeResource;
use App\Services\EventService;
use App\Services\ChargeService;
use Carbon\Carbon;

class ChargeServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreate()
    {
        $evnPar = [
            'amount' => 98765.43,
            'user_id' => 1,
            'currency_id' => 1,
            'type_id' => 1,
        ];
        $eventService = new EventService();
        $event = $eventService->create($evnPar['amount'], $evnPar['user_id'], $evnPar['currency_id'], $evnPar['type_id']);
        $chargeService = new ChargeService();
        $charge = $chargeService->create($event->id);
        $currency = Currency::findOrFail($evnPar['currency_id']);
        $this->assertEquals($charge->amount, $evnPar['amount'] * $currency->value);
        $this->assertEquals($charge->debt_amount, $evnPar['amount'] * $currency->value);
        $this->assertEquals($charge->user_id, $evnPar['user_id']);
        $this->assertEquals($charge->event_id, $event->id);
        \DB::beginTransaction();
        try {
            Charge::where(['event_id' => $event->id])->delete();
            $event->delete();
        } catch (Exception $e) {
            \DB::rollback();
        }
        \DB::commit();
    }

    /**
     * @return void
     */
    public function testGetAll()
    {
        $from = Carbon::now()->subWeek();
        $to = Carbon::now();
        $chargeService = new ChargeService();
        $chargesFromService = $chargeService->getAll();
        $charges = ChargeResource::collection(Charge::get());
        $this->assertEquals($chargesFromService, $charges);

        $chargesFromService = $chargeService->getAll($from);
        $charges = ChargeResource::collection(Charge::where('created_at', '>=', $from)->get());
        $this->assertEquals($chargesFromService, $charges);

        $chargesFromService = $chargeService->getAll(null, $to);
        $charges = ChargeResource::collection(Charge::where('created_at', '<=', $to)->get());
        $this->assertEquals($chargesFromService, $charges);

        $chargesFromService = $chargeService->getAll($from, $to);
        $charges = ChargeResource::collection(
            Charge::where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->get());
        $this->assertEquals($chargesFromService, $charges);
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $chargeService = new ChargeService();
        $chrServ = $chargeService->get(1);
        $charge = new ChargeResource(Charge::findOrFail(1));
        $this->assertEquals($charge, $chrServ);
    }
}
