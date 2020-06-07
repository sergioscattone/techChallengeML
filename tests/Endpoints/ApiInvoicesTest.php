<?php

namespace Tests\Endpoints;

use Tests\TestCase;

class ApiInvoicesTest extends TestCase
{
    /**
     * Endpoint GET invoices
     *
     * @return void
     */
    public function testApiGetInvoices()
    {
        $endpoint = '/api/invoices';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }

    /**
     * Endpoint GET single invoice
     *
     * @return void
     */
    public function testApiGetSingleInvoice()
    {
        $endpoint = '/api/invoices/1';
        $response = $this->get($endpoint);
        $response->assertStatus(401);

        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $response->assertStatus(200);
    }
}
