<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'value' => $this->value,
            'min_order_amount' => $this->min_order_amount,
            'usage_limit' => $this->usage_limit,
            'used_count' => $this->used_count,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'is_active' => $this->is_active,
            'applicable_variant_ids' => $this->whenLoaded('productVariants', fn () => $this->productVariants->pluck('id')),
            'applicable_category_ids' => $this->whenLoaded('categories', fn () => $this->categories->pluck('id')),
        ];
    }
}
