<?php

namespace App\Observers;

use App\Models\PriceHistory;
use Carbon\Carbon;

class PriceHistoryObserver
{
    public function creating(PriceHistory $priceHistory): void
    {
        $validFrom = Carbon::parse($priceHistory->valid_from ?? now());

        PriceHistory::where('tenant_product_variant_id', $priceHistory->tenant_product_variant_id)
            ->where('valid_from', '<=', $validFrom)
            ->whereNull('valid_to')
            ->update(['valid_to' => $validFrom]);
    }
}
