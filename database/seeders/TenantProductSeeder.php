<?php

namespace Database\Seeders;

use App\Models\GlobalProduct;
use App\Models\GlobalProductRequest;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantIds = Tenant::pluck('id');

        foreach ($tenantIds as $tenantId) {
            $numberOfProducts = rand(2, 4);

            for ($i = 0; $i < $numberOfProducts; $i++) {
                $useGlobalProduct = rand(1, 100) <= 60;
                $useGlobalProductRequest = rand(1, 100) <= 60;

                $globalProduct = $useGlobalProduct
                    ? GlobalProduct::select(['id', 'name', 'description'])->inRandomOrder()->first()
                    : null;

                $productName = $globalProduct ? $globalProduct->name : fake()->words(3, true);
                $productDesc = $globalProduct ? $globalProduct->description : fake()->sentence();

                $requestId = $useGlobalProductRequest ? GlobalProductRequest::inRandomOrder()->value('id') : null;

                $tenantProduct = TenantProduct::create([
                    'tenant_id' => $tenantId,
                    'global_product_id' => $globalProduct?->id,
                    'global_product_request_id' => $requestId,
                    'name' => ucfirst($productName),
                    'description' => $productDesc,
                    'is_active' => fake()->boolean(80),
                ]);

                $numberOfVariants = rand(1, 3);

                for ($v = 0; $v < $numberOfVariants; $v++) {
                    $basePrice = fake()->randomFloat(2, 50, 1500);
                    $hasDiscount = fake()->boolean(30);

                    $variant = TenantProductVariant::create([
                        'tenant_product_id' => $tenantProduct->id,
                        'name' => ucfirst($productName)." - {$v}",
                        'sku' => strtoupper(Str::random(8)),
                        'ean' => fake()->ean13(),
                        'price' => $hasDiscount ? ($basePrice * 0.8) : $basePrice,
                        'original_price' => $hasDiscount ? $basePrice : null,
                        'stock_quantity' => fake()->numberBetween(0, 50),
                    ]);

                    $skipImage = fake()->boolean(90);
                    if ($skipImage) continue;

                    try {
                        $placeholderText = urlencode($tenantProduct->name . ' - ' . ($v + 1));
                        $variant->addMediaFromUrl("https://placehold.co/600x400.jpeg?text={$placeholderText}")
                            ->toMediaCollection('tenant_product_variants');
                    } catch (\Exception $e) {
                        $this->command->warn("Failed to download img for: {$variant->sku}");
                    }
                }
            }
        }
    }
}
