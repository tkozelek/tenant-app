<?php

namespace App\Observers;

use App\Models\Bundle;
use Carbon\Carbon;

class BundleObserver
{
    public function updated(Bundle $bundle): void {
        if ($bundle->wasChanged(['price', 'original_price'])) {
            $now = Carbon::now();

            $bundle->priceHistories()
                ->whereNull('valid_to')
                ->update(['valid_to' => $now]);

            $bundle->priceHistories()->create([
                'price' => $bundle->price,
                'original_price' => $bundle->original_price,
                'user_id' => auth()->user()->id ?? null,
                'valid_from' => $now,
                'valid_to' => null,
            ]);
        }
    }

    public function created(Bundle $bundle): void {
        $bundle->priceHistories()->create([
            'price' => $bundle->price,
            'original_price' => $bundle->original_price,
            'user_id' => auth()->user()->id ?? null,
            'valid_from' => Carbon::now(),
            'valid_to' => null,
        ]);
    }
}
