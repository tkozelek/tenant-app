<?php

namespace App\Observers;

use App\Enums\StockHistoryType;
use App\Models\TenantProductVariant;

class TenantProductVariantObserver
{
    public function created(TenantProductVariant $productVariant): void
    {
        if ($productVariant->stock_quantity > 0) {
            $productVariant->stockHistories()->create([
                'type' => StockHistoryType::Adjustment,
                'quantity' => $productVariant->stock_quantity,
                'note' => 'initial stock',
                'user_id' => auth()->user()->id ?? null,
            ]);
        }
    }
}
