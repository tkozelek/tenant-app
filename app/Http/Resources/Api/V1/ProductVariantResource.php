<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'ean' => $this->ean,
            'stock_quantity' => $this->stock_quantity,
            'current_price' => $this->current_price,
            'current_price_formatted' => $this->current_price_formatted,
            'current_original_price' => $this->current_original_price,
            'quantity_prices' => $this->whenLoaded('activeQuantityPrices', fn () => $this->activeQuantityPrices->map(fn ($qp) => [
                'min_quantity' => $qp->min_quantity,
                'max_quantity' => $qp->max_quantity,
                'price' => (float) $qp->price,
            ])->values()),
            'attributes' => $this->whenLoaded('variantAttributes', fn () => $this->variantAttributes->map(fn ($va) => [
                'name' => $va->attribute?->name,
                'value' => $va->attributeValue?->value ?? $va->custom_value,
            ])->values()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
