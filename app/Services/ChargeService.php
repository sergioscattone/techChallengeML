<?php
namespace App\Services;

use App\Http\Resources\ChargeResource;
use App\Services\UserFinantialStatusService;
use App\Models\Event;
use App\Models\Charge;

class ChargeService {
    public function create($event_id) {
        $eventModel = new Event();
        $event = $eventModel->findOrFail($event_id);
        $chargeModel = new Charge();
        $chargeModel->amount = $event->amount * $event->currency->value;
        $chargeModel->debt_amount = $chargeModel->amount;
        $chargeModel->user_id = $event->user_id;
        $chargeModel->event_id = $event->id;
        $chargeModel->save();
        (new UserFinantialStatusService)->update($event->user_id);
        return $chargeModel;
    }

    public function getAll($from = null, $to = null, $invoiceId = null, $userId = null, $eventId = null) {
        $chargeModel = new Charge();
        if ($from) {
            $chargeModel = $chargeModel->where('created_at', '>=', $from);
        }
        if ($to) {
            $chargeModel = $chargeModel->where('created_at', '<=', $to);
        }
        if ($invoiceId) {
            $chargeModel = $chargeModel->where('invoice_id', $invoiceId);
        }
        if ($userId) {
            $chargeModel = $chargeModel->where('user_id', $userId);
        }
        if ($eventId) {
            $chargeModel = $chargeModel->where('event_id', $eventId);
        }
        return ChargeResource::collection($chargeModel->get());
    }

    public function get($id) {
        return new ChargeResource(Charge::findOrFail($id));
    }
}
