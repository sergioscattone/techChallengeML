<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller {

    public function __construct(PaymentService $service) {
        $this->service = $service;
    }

    public function create(Request $request) {
        $amount = $request->get('amount');
        $userId = $request->get('user_id');
        $response = $this->service->create($amount, $userId);
        return response()->json($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request) {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $userId = $request->get('user_id', null);
        return response()->json($this->service->getAll($from, $to, $userId));
    }
}
