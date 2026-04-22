<?php

namespace Database\Factories;

use App\Models\TenantProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductQuantityPrice>
 */
class ProductQuantityPriceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_product_variant_id' => TenantProductVariant::factory(),
            'min_quantity' => 1,
            'max_quantity' => null,
            'price' => fake()->randomFloat(2, 1, 100),
            'valid_from' => now()->subDay(),
            'valid_to' => null,
        ];
    }

    public function active(): static
    {
        return $this->state([
            'valid_from' => now()->subDay(),
            'valid_to' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'valid_from' => now()->subDays(10),
            'valid_to' => now()->subDay(),
        ]);
    }
}
