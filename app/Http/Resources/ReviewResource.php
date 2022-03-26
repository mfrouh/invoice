<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'review'   => $this->review,
            'rate'     => $this->rate,
            'customer' => UserResource::make($this->whenLoaded('customer')),
            'product'  => ProductResource::make($this->whenLoaded('product')),
        ];
    }
}
