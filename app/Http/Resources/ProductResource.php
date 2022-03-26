<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name'        => $this->name,
            'status'      => $this->status,
            'sku'         => $this->sku,
            'slug'        => $this->slug,
            'price'       => $this->price,
            'image'       => $this->image,
            'description' => $this->description,
            'category'    => CategoryResource::make($this->whenLoaded('category')),
            'offer'       => OfferResource::make($this->whenLoaded('offer')),
            'variants'    => VariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
