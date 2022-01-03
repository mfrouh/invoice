<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'sku' => $this->sku,
            'variant_id' => $this->variant_id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'details' => $this->details,
            'total_price' => $this->total_price,
        ];
    }
}
