<?php

namespace App\Http\Resources;
use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'currency' => $this->currency->name,
            'type' => $this->type->charge_group,
            'subtype' => $this->type->name,
            'amount' => $this->amount,
            'date' => (new Carbon($this->created_at))->format('Y-m-d H:i:s'),
        ];
    }
}
