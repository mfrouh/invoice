<?php

namespace App\Http\Resources;

use App\Http\Resources\VariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ValueResource extends JsonResource
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
            'value' => $this->value,
            'attribute_id' => $this->attribute_id,
            'variants' => VariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
