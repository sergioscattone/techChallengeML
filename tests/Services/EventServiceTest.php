<?php

namespace Tests\Services;

use Tests\TestCase;
use App\Models\Charge;
use App\Models\Event;
use App\Models\Currency;
use App\Http\Resources\EventResource;
use App\Services\EventService;
use App\Services\ChargeService;
use Carbon\Carbon;

class EventServiceTest extends TestCase
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
        $this->assertEquals($event->amount, $evnPar['amount']);
        $this->assertEquals($event->user_id, $evnPar['user_id']);
        $this->assertEquals($event->currency_id, $evnPar['currency_id']);
        $this->assertEquals($event->type_id, $evnPar['type_id']);

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
        $eventService = new EventService();
        $eventsFromService = $eventService->getAll();
        $events = EventResource::collection(Event::get());
        $this->assertEquals($eventsFromService, $events);

        $eventsFromService = $eventService->getAll($from);
        $events = EventResource::collection(Event::where('created_at', '>=', $from)->get());
        $this->assertEquals($eventsFromService, $events);

        $eventsFromService = $eventService->getAll(null, $to);
        $events = EventResource::collection(Event::where('created_at', '<=', $to)->get());
        $this->assertEquals($eventsFromService, $events);

        $eventsFromService = $eventService->getAll($from, $to);
        $events = EventResource::collection(
            Event::where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->get());
        $this->assertEquals($eventsFromService, $events);
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $eventService = new EventService();
        $evnServ = $eventService->get(1);
        $event = new EventResource(Event::findOrFail(1));
        $this->assertEquals($event, $evnServ);
    }
}
