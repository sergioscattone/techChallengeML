<?php
namespace App\Services;

use App\Http\Resources\InvoiceResource;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Charge;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceService {
    public function consolidate($month) {
        $from = Carbon::createFromFormat('Y-m-d', $month.'-01')->toDateTimeString();
        $to = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateTimeString();
        $ChargeModel = new Charge();
        $userCharges = $ChargeModel
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('user_id')
            ->get([
                'user_id',
                \DB::raw('SUM(charges.amount) as amount'),
                \DB::raw('SUM(charges.debt_amount) as debt_amount')
            ]);
        foreach($userCharges as $userCharge) {
            \DB::beginTransaction();
            try {
                $InvoiceModel = new Invoice();
                $Invoice = $InvoiceModel
                    ->firstOrNew(['user_id' => $userCharge->user_id, 'month' => $month]);
                $Invoice->month = $from;
                $Invoice->amount = $userCharge->amount;
                $Invoice->debt_amount = $userCharge->debt_amount;
                $Invoice->user_id = $userCharge->user_id;
                $Invoice->save();
                \DB::update('update charges set invoice_id = '.$Invoice->id.
                    ' where user_id = ? and created_at between ? and ?',
                    [$userCharge->user_id, $from, $to]);
            } catch (Exception $e) {
                \DB::rollback();
                throw new HttpException(500, 'There was an error processing the invoice');
            }
            \DB::commit();
        }
    }

    public function getAll($from = null, $to = null, $userId = null) {
        $invoiceModel = new Invoice();
        if ($from) {
            $invoiceModel = $invoiceModel->where('month', '>=', $from.'-01');
        }
        if ($to) {
            $invoiceModel = $invoiceModel->where('month', '<=', $to.'-01');
        }
        if ($userId) {
            $invoiceModel = $invoiceModel->where('user_id', $userId);
        }
        return InvoiceResource::collection($invoiceModel->get());
    }

    public function get($id) {
        return new InvoiceResource(Invoice::findOrFail($id));
    }
}
