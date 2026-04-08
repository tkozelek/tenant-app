<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuantityPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'min_quantity' => $this->min_quantity,
            'max_quantity' => $this->max_quantity,
            'price' => (float) $this->price,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
        ];
    }
}
