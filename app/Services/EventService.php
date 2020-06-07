<?php
namespace App\Services;

use App\Http\Resources\EventResource;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Event;
use App\Services\ChargeService;
use App\Models\User;
use App\Models\Currency;
use App\Models\EventType;

class EventService {
    public function create($amount, $userId, $currencyId, $typeId) {
        $this->validate($amount, $userId, $currencyId, $typeId);
        $eventModel = new Event();
        $eventModel->amount = $amount;
        $eventModel->user_id = $userId;
        $eventModel->currency_id = $currencyId;
        $eventModel->type_id = $typeId;
        $eventModel->save();

        $chargeService = new ChargeService();
        $chargeService->create($eventModel->id);

        return new EventResource($eventModel);
    }

    private function validate($amount, $userId, $currencyId, $typeId) {
        if (empty($amount) || !is_numeric($amount) || empty($userId) || empty($currencyId) || empty($typeId)) {
            throw new HttpException(400, 'missing parameters: you should have: amount, user_id, currency_id and type_id');
        }
        if ($amount <= 0) {
            throw new HttpException(400, 'amount is less than zero');
        }
        $userModel = new User();
        $userModel->findOrFail($userId);
        $currencyModel = new Currency();
        $currencyModel->findOrFail($currencyId);
        $eventTypeModel = new EventType();
        $eventTypeModel->findOrFail($typeId);
    }

    public function getAll($from = null, $to = null) {
        $eventModel = new Event();
        if ($from) {
            $eventModel = $eventModel->where('created_at', '>=', $from);
        }
        if ($to) {
            $eventModel = $eventModel->where('created_at', '<=', $to);
        }
        return EventResource::collection($eventModel->get());
    }

    public function get($id) {
        return new EventResource(Event::findOrFail($id));
    }
}
