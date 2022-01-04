<?php

namespace App\Http\Resources;

use App\Http\Resources\ValueResource;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            'product_id' => ProductResource::make($this->whenLoaded('product')),
            'values' => ValueResource::collection($this->whenLoaded('values')),
        ];
    }
}
