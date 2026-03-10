<?php

namespace App\Observers;

use App\Models\TenantProductVariant;
use Carbon\Carbon;

class TenantProductVariantObserver
{
    public function updated(TenantProductVariant $productVariant): void
    {
        if ($productVariant->wasChanged('price') || $productVariant->wasChanged('original_price')) {
            $now = Carbon::now();

            $productVariant->priceHistories()
                ->whereNull('valid_to')
                ->update(['valid_to' => $now]);

            $productVariant->priceHistories()->create([
                'price' => $productVariant->price,
                'original_price' => $productVariant->original_price,
                'user_id' => auth()->user()->id ?? null,
                'valid_from' => $now,
                'valid_to' => null,
            ]);
        }
    }

    public function created(TenantProductVariant $productVariant): void
    {
        if ($productVariant->stock_quantity > 0) {
            $productVariant->stockHistories()->create([
                'type' => 'adjustment',
                'quantity' => $productVariant->stock_quantity,
                'note' => 'initial stock',
                'user_id' => auth()->user()->id ?? null,
            ]);
        }

        if ($productVariant->price > 0) {
            $productVariant->priceHistories()->create([
                'price' => $productVariant->price,
                'valid_from' => Carbon::now(),
                'user_id' => auth()->user()->id ?? null,
                'valid_to' => null,
            ]);
        }
    }
}
