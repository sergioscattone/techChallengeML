<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller {

    public function __construct(EventService $service) {
        $this->service = $service;
    }

    public function create(Request $request) {
        $amount = $request->get('amount');
        $userId = $request->get('user_id');
        $currencyId = $request->get('currency_id');
        $typeId = $request->get('type_id');
        $response = $this->service->create($amount, $userId, $currencyId, $typeId);
        return response()->json($response);
    }
}
