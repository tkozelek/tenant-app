<?php

namespace App\Observers;

use App\Models\ProductQuantityPrice;
use Illuminate\Support\Facades\DB;

class ProductQuantityPriceObserver
{
    public function updating(ProductQuantityPrice $quantityPrice): void
    {
        if (! $quantityPrice->isDirty(['min_quantity', 'max_quantity', 'price'])) {
            return;
        }

        DB::table('product_quantity_prices')->insert([
            'tenant_product_variant_id' => $quantityPrice->tenant_product_variant_id,
            'min_quantity' => $quantityPrice->getOriginal('min_quantity'),
            'max_quantity' => $quantityPrice->getOriginal('max_quantity'),
            'price' => $quantityPrice->getOriginal('price'),
            'valid_from' => $quantityPrice->getOriginal('valid_from'),
            'valid_to' => now(),
            'created_at' => $quantityPrice->created_at,
            'updated_at' => now(),
            'deleted_at' => now(),
        ]);

        $quantityPrice->valid_from = now();
    }

    public function deleted(ProductQuantityPrice $quantityPrice): void
    {
        DB::table('product_quantity_prices')
            ->where('id', $quantityPrice->id)
            ->update(['valid_to' => now()]);
    }
}
