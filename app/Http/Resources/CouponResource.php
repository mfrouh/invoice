<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'id'              => $this->id,
            'code'            => $this->code,
            'start'           => $this->start,
            'end'             => $this->end,
            'condition'       => $this->condition,
            'condition_value' => $this->condition_value,
            'type'            => $this->type,
            'value'           => $this->value,
            'message'         => $this->message,
            'times'           => $this->times,
            'customers'       => UserResource::collection($this->whenLoaded('customers')),
        ];
    }
}
