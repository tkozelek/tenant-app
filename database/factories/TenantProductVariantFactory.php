<?php

namespace Database\Factories;

use App\Models\TenantProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TenantProductVariant>
 */
class TenantProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_product_id' => TenantProduct::factory(),
            'name' => fake()->words(2, true),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-???')),
            'ean' => fake()->optional()->ean13(),
            'url' => fake()->optional()->url(),
            'stock_quantity' => fake()->numberBetween(0, 100),
        ];
    }

    public function inStock(int $quantity = 10): static
    {
        return $this->state(['stock_quantity' => $quantity]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock_quantity' => 0]);
    }
}
