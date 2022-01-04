<?php

namespace App\Http\Resources;

use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'details' => $this->details,
            'total_price' => $this->total_price,
            'order' => OrderResource::make($this->whenLoaded('order')),
            'product' => ProductResource::make($this->whenLoaded('product')),
        ];
    }
}
