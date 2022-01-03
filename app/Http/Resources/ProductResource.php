<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'status' => $this->status,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'price' => $this->price,
            'image' => $this->image,
            'description' => $this->description,
            'category_id' => $this->category_id,
        ];
    }
}
