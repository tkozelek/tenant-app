<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->current_price,
            'original_price' => $this->current_original_price,
            'is_active' => $this->is_active,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'variant' => $item->relationLoaded('variant') ? [
                    'id' => $item->variant->id,
                    'name' => $item->variant->name,
                    'sku' => $item->variant->sku,
                ] : null,
            ])),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
