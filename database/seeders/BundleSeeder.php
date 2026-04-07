<?php

namespace Database\Seeders;

use App\Models\Bundle;
use App\Models\Tenant;
use App\Models\TenantProductVariant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BundleSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::with('owner')->get();

        foreach ($tenants as $tenant) {
            $variantIds = TenantProductVariant::query()
                ->whereHas('product', fn ($q) => $q->where('tenant_id', $tenant->id)->where('is_active', true))
                ->where('stock_quantity', '>', 0)
                ->pluck('id');

            if ($variantIds->count() < 2) {
                continue;
            }

            $bundleCount = rand(2, 3);

            for ($i = 0; $i < $bundleCount; $i++) {
                $itemCount = min(rand(2, 4), $variantIds->count());
                $selectedIds = $variantIds->shuffle()->take($itemCount);

                $selectedVariants = TenantProductVariant::with('activePriceHistory')
                    ->whereIn('id', $selectedIds)
                    ->get();

                $totalPrice = $selectedVariants->sum(fn ($v) => (float) ($v->activePriceHistory?->price ?? 0));

                if ($totalPrice <= 0) {
                    continue;
                }

                $bundlePrice = round($totalPrice * 0.82, 2);
                $name = "Bundle {$tenant->name} #".($i + 1);

                $bundle = Bundle::withoutEvents(function () use ($tenant, $name, $bundlePrice, $totalPrice): Bundle {
                    return Bundle::create([
                        'tenant_id' => $tenant->id,
                        'name' => $name,
                        'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
                        'description' => 'Výhodný balík produktov so zľavou 18 %.',
                        'price' => $bundlePrice,
                        'original_price' => round($totalPrice, 2),
                        'is_active' => true,
                    ]);
                });

                $bundle->priceHistories()->create([
                    'price' => $bundle->price,
                    'original_price' => $bundle->original_price,
                    'user_id' => $tenant->owner_id,
                    'valid_from' => Carbon::now(),
                    'valid_to' => null,
                ]);

                foreach ($selectedVariants as $variant) {
                    $bundle->items()->create([
                        'tenant_product_variant_id' => $variant->id,
                        'quantity' => rand(1, 2),
                    ]);
                }
            }
        }
    }
}
