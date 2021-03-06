<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ValueResource extends JsonResource
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
            'id'        => $this->id,
            'value'     => $this->value,
            'attribute' => AttributeResource::make($this->whenLoaded('attribute')),
            'variants'  => VariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
