<?php

namespace App\Http\Controllers;

use App\Services\UserFinantialStatusService;

class UserFinantialStatusController extends Controller {

    public function __construct(UserFinantialStatusService $service) {
        $this->service = $service;
    }

    public function getByUserId($userId) {
        return response($this->service->getByUserId($userId));
    }
}
