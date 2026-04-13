<?php

namespace Database\Seeders;

use App\Models\GlobalProduct;
use App\Models\Tenant;
use App\Models\TenantProduct;
use Illuminate\Database\Seeder;

class TenantProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantIds = Tenant::pluck('id');

        foreach ($tenantIds as $tenantId) {
            $numberOfProducts = rand(5, 8);

            for ($i = 0; $i < $numberOfProducts; $i++) {
                $globalProduct = fake()->boolean(90)
                    ? GlobalProduct::select(['id', 'name', 'description'])->inRandomOrder()->first()
                    : null;

                $productName = $globalProduct ? $globalProduct->name : fake()->words(3, true);
                $productDesc = $globalProduct ? $globalProduct->description : fake()->sentence();

                $name = ucfirst($productName);

                $tenantProduct = TenantProduct::firstOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'slug' => str($name)->slug(),
                    ],
                    [
                        'global_product_id' => $globalProduct?->id,
                        'global_product_request_id' => null,
                        'name' => $name,
                        'description' => $productDesc,
                        'is_active' => true,
                    ]
                );

                if ($tenantProduct->wasRecentlyCreated && ! fake()->boolean(90)) {
                    try {
                        $placeholderText = urlencode($name);
                        $tenantProduct->addMediaFromUrl("https://placehold.co/600x400.jpeg?text={$placeholderText}")
                            ->toMediaCollection('tenant_products');
                    } catch (\Exception $e) {
                        $this->command->warn("Failed to download img for: {$name}");
                    }
                }
            }
        }
    }
}
