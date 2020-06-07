<?php

namespace Tests\Consistency;

use Tests\TestCase;
use App\Models\UserFinantialStatus;
use App\Http\Resources\UserFinantialStatusResource;
use Carbon\Carbon;

class ApiUserStatusesConsistencyTest extends TestCase
{
    /**
     * @return void
     */
    public function testApiGetPaymentsConsistencyNoParams()
    {
        $endpoint = '/api/users/status';
        $payments = json_encode(UserFinantialStatusResource::collection(UserFinantialStatus::get())->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $respUserStatuses = $response->getContent();
        $this->assertEquals($payments, $respUserStatuses);
    }

    /**
     * @return void
     */
    public function testApiGetConsistencySingleUserStatus()
    {
        $userFinantialStatusId = 1;
        $endpoint = '/api/users/status/'.$userFinantialStatusId;
        $resource = new UserFinantialStatusResource(UserFinantialStatus::findOrFail($userFinantialStatusId));
        $usrFinStatus = json_encode($resource->resolve($resource));
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, ['token' => $token]);
        $respUsrFinStatus = $response->getContent();
        $this->assertEquals($usrFinStatus, $respUsrFinStatus);
    }

    /**
     * @return void
     */
    public function testApiGetConsistencySingleUserStatusByUserId()
    {
        $userId = 1;
        $endpoint = '/api/users/'.$userId.'/status/';
        $userFinantialStatusDB = UserFinantialStatus::where('user_id', $userId)->get()[0];
        $resource = new UserFinantialStatusResource($userFinantialStatusDB);
        $usrFinStatus = json_encode($resource->resolve($resource));
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, ['token' => $token]);
        $respUsrFinStatus = $response->getContent();
        $this->assertEquals($usrFinStatus, $respUsrFinStatus);
    }
}
