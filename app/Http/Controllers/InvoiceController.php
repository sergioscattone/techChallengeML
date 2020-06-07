<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller {

    public function __construct(InvoiceService $service) {
        $this->service = $service;
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
