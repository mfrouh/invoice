<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
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
            'id'       => $this->id,
            'sku'      => $this->sku,
            'price'    => $this->price,
            'quantity' => $this->quantity,
            'product'  => ProductResource::make($this->whenLoaded('product')),
            'values'   => ValueResource::collection($this->whenLoaded('values')),
        ];
    }
}
