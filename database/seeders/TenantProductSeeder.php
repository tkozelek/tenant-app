<?php

namespace Database\Seeders;

use App\Models\GlobalProduct;
use App\Models\GlobalProductRequest;
use App\Models\PriceHistory;
use App\Models\StockHistory;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Carbon\Carbon;
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
                $globalProduct = fake()->boolean(90)
                    ? GlobalProduct::select(['id', 'name', 'description'])->inRandomOrder()->first()
                    : null;

                $productName = $globalProduct ? $globalProduct->name : fake()->words(3, true);
                $productDesc = $globalProduct ? $globalProduct->description : fake()->sentence();

                $name = ucfirst($productName);

                $tenantProduct = TenantProduct::create([
                    'tenant_id' => $tenantId,
                    'global_product_id' => $globalProduct?->id,
                    'global_product_request_id' => null,
                    'name' => $name,
                    'description' => $productDesc,
                    'is_active' => fake()->boolean(80),
                ]);

                if (!fake()->boolean(90)) {
                    try {
                        $placeholderText = urlencode($name);
                        $tenantProduct->addMediaFromUrl("https://placehold.co/600x400.jpeg?text={$placeholderText}")
                            ->toMediaCollection('tenant_product');
                    } catch (\Exception $e) {
                        $this->command->warn("Failed to download img for: {$name}");
                    }
                }
            }
        }
    }
}
