<?php

namespace Database\Seeders;

use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $products = TenantProduct::all();

        foreach ($products as $product) {
            $numberOfVariants = rand(1, 3);

            for ($v = 0; $v < $numberOfVariants; $v++) {
                $basePrice = fake()->randomFloat(2, 50, 1500);
                $hasDiscount = fake()->boolean(30);
                // https://laravel.com/docs/12.x/eloquent#muting-events
                $variant = TenantProductVariant::withoutEvents(function () use ($product, $v, $basePrice, $hasDiscount) {

                    $newVariant = $product->variants()->create([
                        'name' => $product->name." - {$v}",
                        'sku' => strtoupper(Str::random(8)),
                        'ean' => fake()->ean13(),
                        'price' => $hasDiscount ? ($basePrice * 0.8) : $basePrice,
                        'original_price' => $hasDiscount ? $basePrice : null,
                        'stock_quantity' => 0,
                    ]);

                    $this->generatePriceHistory($newVariant, $basePrice);
                    $this->generateStockHistory($newVariant);

                    return $newVariant;
                });

                if (! fake()->boolean(90)) {
                    try {
                        $placeholderText = urlencode($product->name.' - '.($v + 1));
                        $variant->addMediaFromUrl("https://placehold.co/600x400.jpeg?text={$placeholderText}")
                            ->toMediaCollection('tenant_product_variants');
                    } catch (\Exception $e) {
                        $this->command->warn("Failed to download img for: {$variant->sku}");
                    }
                }
            }
        }
    }

    private function generatePriceHistory(TenantProductVariant $variant, float $initialPrice): void
    {
        $price = $initialPrice;
        $date = Carbon::now()->subMonths(6);
        $now = Carbon::now();

        $finalPrice = $initialPrice;
        $finalOriginalPrice = null;

        while (true) {
            $daysToAdd = rand(15, 30);

            if ($date->copy()->addDays($daysToAdd)->isAfter($now)) {
                break;
            }

            $date->addDays($daysToAdd);
            $priceChangePercentage = rand(-15, 20) / 100;
            $price *= (1 + $priceChangePercentage);
            $price = round($price, 2);

            if ($price <= 0) {
                $price = $initialPrice;
            }

            if (fake()->boolean(30)) {
                $finalOriginalPrice = $price;
                $discountMultiplier = rand(70, 90) / 100;
                $finalPrice = round($price * $discountMultiplier, 2);
            } else {
                $finalOriginalPrice = null;
                $finalPrice = $price;
            }

            $variant->priceHistories()->create([
                'price' => $finalPrice,
                'original_price' => $finalOriginalPrice,
                'valid_from' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
        $variant->update([
            'price' => $price,
            'original_price' => $finalOriginalPrice,
        ]);
    }

    private function generateStockHistory(TenantProductVariant $variant): void
    {
        $currentRunningStock = 0;
        $date = Carbon::now()->subMonths(6);
        $now = Carbon::now();

        while (true) {
            $daysToAdd = rand(15, 30);

            if ($date->copy()->addDays($daysToAdd)->isAfter($now)) {
                break;
            }

            $date->addDays($daysToAdd);
            $isIncrease = ($currentRunningStock <= 0) || fake()->boolean(50);

            if ($isIncrease) {
                $change = rand(10, 30);
                $type = 'purchase';
            } else {
                $change = -rand(1, $currentRunningStock);
                $type = 'sale';
            }

            if (fake()->boolean(20)) {
                $type = 'adjustment';
            }

            $currentRunningStock += $change;

            $variant->stockHistories()->create([
                'quantity' => $change,
                'type' => $type,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        $variant->update(['stock_quantity' => $currentRunningStock]);
    }
}
