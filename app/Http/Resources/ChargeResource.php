<?php

namespace App\Http\Resources;
use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;

class ChargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'debt_amount' => $this->debt_amount,
            'invoice_id' => $this->invoice_id,
            'date' => (new Carbon($this->created_at))->format('Y-m-d H:i:s'),
        ];
    }
}
