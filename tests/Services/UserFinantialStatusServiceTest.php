<?php

namespace Tests\Services;

use Tests\TestCase;
use App\Models\UserFinantialStatus;
use App\Http\Resources\UserFinantialStatusResource;
use App\Services\UserFinantialStatusService;
use App\Services\EventService;
use Carbon\Carbon;

class UserFinantialStatusServiceTest extends TestCase
{

    /**
     * @return void
     */
    public function testUpdate()
    {
        \DB::beginTransaction();
        // adding debt without any charge
        $usrFinStatus = UserFinantialStatus::findOrFail(1);
        $originalDebt = $usrFinStatus->debt;
        $usrFinStatus->debt += rand(1, 100);
        $usrFinStatus->save();

        // update debt according to charges
        $usrFinStatusService = new UserFinantialStatusService();
        $usrFinStatusService->update($usrFinStatus->user_id);

        // debt should be original debt
        $usrFinStatus = UserFinantialStatus::findOrFail(1);
        $this->assertEquals($usrFinStatus->debt, $originalDebt);
        \DB::commit();
    }

    /**
     * @return void
     */
    public function testGetAll()
    {
        $from = Carbon::now()->subWeek();
        $to = Carbon::now();
        $usrFinStatusService = new UserFinantialStatusService();
        $usrFinStatusFromService = $usrFinStatusService->getAll();
        $usrFinStatuses = UserFinantialStatusResource::collection(UserFinantialStatus::get());
        $this->assertEquals($usrFinStatusFromService, $usrFinStatuses);
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $usrFinStatusService = new UserFinantialStatusService();
        $payServ = $usrFinStatusService->get(1);
        $usrFinStatus = new UserFinantialStatusResource(UserFinantialStatus::findOrFail(1));
        $this->assertEquals($usrFinStatus, $payServ);
    }

    /**
     * @return void
     */
    public function testGetByUserId()
    {
        $usrFinStatusService = new UserFinantialStatusService();
        $usrFinStatus = $usrFinStatusService->getByUserId(1);
        $usrFinStatus = new UserFinantialStatusResource(UserFinantialStatus::where('user_id', 1)->get()[0]);
        $this->assertEquals($usrFinStatus, $usrFinStatus);
    }
}
