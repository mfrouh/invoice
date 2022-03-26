<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'sku'         => $this->sku,
            'name'        => $this->name,
            'price'       => $this->price,
            'quantity'    => $this->quantity,
            'details'     => $this->details,
            'total_price' => $this->total_price,
            'customer'    => UserResource::make($this->whenLoaded('customer')),
            'product'     => ProductResource::make($this->whenLoaded('product')),
            'variant'     => VariantResource::make($this->whenLoaded('variant')),
        ];
    }
}
