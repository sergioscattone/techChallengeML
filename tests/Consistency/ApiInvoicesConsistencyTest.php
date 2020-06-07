<?php

namespace Tests\Consistency;

use Tests\TestCase;
use App\Models\Invoice;
use App\Http\Resources\InvoiceResource;
use Carbon\Carbon;

class ApiInvoicesConsistencyTest extends TestCase
{
    /**
     * @return void
     */
    public function testApiGetInvoicesConsistencyNoParams()
    {
        $endpoint = '/api/invoices';
        $Invoices = json_encode(InvoiceResource::collection(Invoice::get())->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $respInvoices = $response->getContent();
        $this->assertEquals($Invoices, $respInvoices);
    }

    /**
     * @return void
     */
    public function testApiGetInvoicesConsistencyByDate()
    {
        $from = Carbon::now()->subWeek();
        $to = Carbon::now();
        $endpoint = '/api/invoices';
        $invoiceDB = Invoice::where('month', '>=', $from)->where('month', '<=', $to)->get();
        $Invoices = json_encode(InvoiceResource::collection($invoiceDB)->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, [
            'token' => $token,
            'from' => $from,
            'to' => $to,
        ]);
        $respInvoices = $response->getContent();
        $this->assertEquals($Invoices, $respInvoices);
    }

    /**
     * @return void
     */
    public function testApiGetConsistencySingleinvoice()
    {
        $invoiceId = 1;
        $endpoint = '/api/invoices/'.$invoiceId;
        $resource = new InvoiceResource(Invoice::findOrFail($invoiceId));
        $Invoices = json_encode($resource->resolve($resource));
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, ['token' => $token]);
        $respInvoice = $response->getContent();
        $this->assertEquals($Invoices, $respInvoice);
    }
}
