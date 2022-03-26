<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'id'      => $this->id,
            'type'    => $this->type,
            'value'   => $this->value,
            'message' => $this->message,
            'start'   => $this->start,
            'end'     => $this->end,
            'product' => ProductResource::make($this->whenLoaded('product')),
        ];
    }
}
