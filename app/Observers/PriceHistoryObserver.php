<?php

namespace App\Observers;

use App\Models\PriceHistory;
use Carbon\Carbon;

class PriceHistoryObserver
{
    public function creating(PriceHistory $priceHistory): void
    {
        $validFrom = Carbon::parse($priceHistory->valid_from ?? now());
        $variantId = $priceHistory->tenant_product_variant_id;

        PriceHistory::where('tenant_product_variant_id', $variantId)
            ->where('valid_from', '<=', $validFrom)
            ->where(function ($query) use ($validFrom): void {
                $query->whereNull('valid_to')
                    ->orWhere('valid_to', '>', $validFrom);
            })
            ->update(['valid_to' => $validFrom]);

        if ($priceHistory->valid_to === null) {
            $nextPrice = PriceHistory::where('tenant_product_variant_id', $variantId)
                ->where('valid_from', '>', $validFrom)
                ->orderBy('valid_from')
                ->first();

            if ($nextPrice) {
                $priceHistory->valid_to = $nextPrice->valid_from;
            }
        }
    }
}
