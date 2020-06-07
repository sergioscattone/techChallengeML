<?php

namespace Tests\Services;

use Tests\TestCase;
use App\Models\Invoice;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Carbon\Carbon;

class InvoiceServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testConsolidate()
    {
        // @TODO: check it in V2
        $this->assertTrue(true);
    }
    /**
     * @return void
     */
    public function testGetAll()
    {
        $from = Carbon::now()->subWeek();
        $to = Carbon::now();
        $invoiceService = new InvoiceService();
        $invoicesFromService = $invoiceService->getAll();
        $invoices = InvoiceResource::collection(Invoice::get());
        $this->assertEquals($invoicesFromService, $invoices);

        $invoicesFromService = $invoiceService->getAll($from);
        $invoices = InvoiceResource::collection(Invoice::where('month', '>=', $from)->get());
        $this->assertEquals($invoicesFromService, $invoices);

        $invoicesFromService = $invoiceService->getAll(null, $to);
        $invoices = InvoiceResource::collection(Invoice::where('month', '<=', $to)->get());
        $this->assertEquals($invoicesFromService, $invoices);

        $userId = 1;
        $invoicesFromService = $invoiceService->getAll(null, null, $userId);
        $invoices = InvoiceResource::collection(Invoice::where('user_id', $userId)->get());
        $this->assertEquals($invoicesFromService, $invoices);

        $invoicesFromService = $invoiceService->getAll($from, $to);
        $invoices = InvoiceResource::collection(
            Invoice::where('month', '>=', $from)
            ->where('month', '<=', $to)
            ->get());
        $this->assertEquals($invoicesFromService, $invoices);
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $invoiceService = new InvoiceService();
        $invServ = $invoiceService->get(1);
        $invoice = new InvoiceResource(Invoice::findOrFail(1));
        $this->assertEquals($invoice, $invServ);
    }
}
