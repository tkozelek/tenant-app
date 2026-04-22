<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\PriceHistory;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $farbaAttribute = Attribute::where('slug', 'farba')->first();
        $storageAttribute = Attribute::where('slug', 'kapacita-uloziska')->first();

        $products = TenantProduct::with([
            'globalProduct.globalProductAttributes.attribute',
            'globalProduct.globalProductAttributes.attributeValue',
        ])->get();

        foreach ($products as $product) {
            $globalProductAttrs = $product->globalProduct?->globalProductAttributes ?? collect();
            $basePrice = fake()->randomFloat(2, 50, 1500);

            $productName = $product->globalProduct?->name ?? 'Unknown';

            $colorAttrs = $farbaAttribute
                ? $globalProductAttrs->where('attribute_id', $farbaAttribute->id)->values()
                : collect();

            $storageAttr = $storageAttribute
                ? $globalProductAttrs->where('attribute_id', $storageAttribute->id)->first()
                : null;

            if ($colorAttrs->isNotEmpty()) {
                foreach ($colorAttrs as $colorGpa) {
                    TenantProductVariant::withoutEvents(function () use ($product, $colorGpa, $farbaAttribute, $storageAttribute, $storageAttr, $basePrice, $productName) {
                        $variant = $product->variants()->create([
                            'name' => "{$productName} - {$colorGpa->attributeValue->value}",
                            'sku' => strtoupper(Str::random(8)),
                            'ean' => fake()->ean13(),
                            'stock_quantity' => 0,
                        ]);

                        $variant->variantAttributes()->create([
                            'attribute_id' => $farbaAttribute->id,
                            'attribute_value_id' => $colorGpa->attribute_value_id,
                            'custom_value' => null,
                        ]);

                        if ($storageAttr && $storageAttribute) {
                            $variant->variantAttributes()->create([
                                'attribute_id' => $storageAttribute->id,
                                'attribute_value_id' => $storageAttr->attribute_value_id,
                                'custom_value' => $storageAttr->custom_value,
                            ]);
                        }

                        $this->generatePriceHistory($variant, $basePrice);
                        $this->generateStockHistory($variant);

                        if (fake()->boolean(50)) {
                            $this->generateQuantityPrices($variant, $basePrice);
                        }
                    });
                }
            } else {
                TenantProductVariant::withoutEvents(function () use ($product, $storageAttribute, $storageAttr, $basePrice) {
                    $variant = $product->variants()->create([
                        'name' => $product->name ?? 'Random filler product',
                        'sku' => strtoupper(Str::random(8)),
                        'ean' => fake()->ean13(),
                        'stock_quantity' => 0,
                    ]);

                    if ($storageAttr && $storageAttribute) {
                        $variant->variantAttributes()->create([
                            'attribute_id' => $storageAttribute->id,
                            'attribute_value_id' => $storageAttr->attribute_value_id,
                            'custom_value' => $storageAttr->custom_value,
                        ]);
                    }

                    $this->generatePriceHistory($variant, $basePrice);
                    $this->generateStockHistory($variant);

                    if (fake()->boolean(50)) {
                        $this->generateQuantityPrices($variant, $basePrice);
                    }
                });
            }
        }
    }

    private function generatePriceHistory(TenantProductVariant $variant, float $initialPrice): void
    {
        $price = $initialPrice;
        $date = Carbon::now()->subMonths(6);
        $now = Carbon::now();
        $entries = [];

        while (true) {
            $daysToAdd = rand(5, 15);

            if ($date->copy()->addDays($daysToAdd)->isAfter($now)) {
                break;
            }

            $date->addDays($daysToAdd);
            $price = round($price * (1 + rand(-15, 20) / 100), 2);

            if ($price <= 0) {
                $price = $initialPrice;
            }

            $finalOriginalPrice = null;
            $finalPrice = $price;

            if (fake()->boolean(30)) {
                $finalOriginalPrice = $price;
                $finalPrice = round($price * (rand(70, 90) / 100), 2);
            }

            $entries[] = [
                'price' => $finalPrice,
                'original_price' => $finalOriginalPrice,
                'valid_from' => $date->copy(),
            ];
        }

        PriceHistory::withoutEvents(function () use ($variant, $entries): void {
            foreach ($entries as $i => $entry) {
                $validTo = isset($entries[$i + 1]) ? $entries[$i + 1]['valid_from'] : null;

                $variant->priceHistories()->create([
                    'price' => $entry['price'],
                    'original_price' => $entry['original_price'],
                    'valid_from' => $entry['valid_from'],
                    'valid_to' => $validTo,
                    'created_at' => $entry['valid_from'],
                    'updated_at' => $entry['valid_from'],
                ]);
            }
        });
    }

    private function generateQuantityPrices(TenantProductVariant $variant, float $basePrice): void
    {
        $tiers = [
            ['min' => 1, 'max' => 4,    'discount' => 0.95],
            ['min' => 5, 'max' => 9,    'discount' => 0.90],
            ['min' => 10, 'max' => null, 'discount' => 0.82],
        ];

        foreach ($tiers as $tier) {
            $variant->quantityPrices()->create([
                'min_quantity' => $tier['min'],
                'max_quantity' => $tier['max'],
                'price' => round($basePrice * $tier['discount'], 2),
                'valid_from' => Carbon::now()->subMonths(6),
                'valid_to' => null,
            ]);
        }
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
