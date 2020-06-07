<?php

namespace Tests\Endpoints;

use Tests\TestCase;

class ApiChargesTest extends TestCase
{
    /**
     * Endpoint GET changes
     *
     * @return void
     */
    public function testApiGetCharges()
    {
        $endpoint = '/api/charges';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }

    /**
     * Endpoint GET single change
     *
     * @return void
     */
    public function testApiGetSingleCharge()
    {
        $endpoint = '/api/charges/1';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }
}
