<?php

namespace Tests\Endpoints;

use Tests\TestCase;

class ApiUserStatusesTest extends TestCase
{
    /**
     * Endpoint GET user statuses
     *
     * @return void
     */
    public function testApiGetUserStatuses()
    {
        $endpoint = '/api/users/status';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }

    /**
     * Endpoint GET single user status
     *
     * @return void
     */
    public function testApiGetSingleUserStatus()
    {
        $endpoint = '/api/users/status/1';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }

    /**
     * Endpoint GET single user status by user ID
     *
     * @return void
     */
    public function testApiGetSingleUserStatusByUserId()
    {
        $endpoint = '/api/users/1/status';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }
}
