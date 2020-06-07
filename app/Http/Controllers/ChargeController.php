<?php

namespace App\Http\Controllers;

use App\Services\ChargeService;
use Illuminate\Http\Request;

class ChargeController extends Controller {

    public function __construct(ChargeService $service)
    {
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request) {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $invoiceId = $request->get('invoice_id', null);
        $userId = $request->get('user_id', null);
        $eventId = $request->get('event_id', null);
        return response()->json($this->service->getAll($from, $to, $invoiceId, $userId, $eventId));
    }
}
