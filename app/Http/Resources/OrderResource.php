<?php

namespace App\Http\Resources;

use App\Http\Resources\InvoiceResource;
use App\Http\Resources\OrderDetailsResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'total' => $this->total,
            'invoice_qr_code' => $this->invoice_qr_code,
            'tax' => $this->tax,
            'ship' => $this->ship,
            'discount' => $this->discount,
            'orderDetails' => OrderDetailsResource::collection($this->whenLoaded('orderDetails')),
            'invoice' => InvoiceResource::make($this->whenLoaded('invoice')),
        ];
    }
}
