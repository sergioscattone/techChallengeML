<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $service;

    /**
     * Get all
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request) {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        return response($this->service->getAll($from, $to));
    }

    /**
     * Get single
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id) {
        return response()->json($this->service->get($id));
    }
}
