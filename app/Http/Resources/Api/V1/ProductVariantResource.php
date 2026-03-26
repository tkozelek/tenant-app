<?php

namespace App\Http\Resources\Api\V1;

use App\Models\ProductQuantityPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $activeQuantityPrices = $this->whenLoaded(
            'quantityPrices',
            fn () => $this->quantityPrices
                ->filter(fn (ProductQuantityPrice $qp) => $qp->valid_from->lte(now()))
                ->sortBy('min_quantity')
                ->map(fn (ProductQuantityPrice $qp) => [
                    'min_quantity' => $qp->min_quantity,
                    'max_quantity' => $qp->max_quantity,
                    'price' => (float) $qp->price,
                ])
                ->values()
        );

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'ean' => $this->ean,
            'stock_quantity' => $this->stock_quantity,
            'current_price' => $this->current_price,
            'current_price_formatted' => $this->current_price_formatted,
            'current_original_price' => $this->current_original_price,
            'quantity_prices' => $activeQuantityPrices,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
