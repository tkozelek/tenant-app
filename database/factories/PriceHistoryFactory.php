<?php

namespace Database\Factories;

use App\Models\TenantProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceHistory>
 */
class PriceHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_product_variant_id' => TenantProductVariant::factory(),
            'price' => fake()->randomFloat(2, 5, 500),
            'original_price' => null,
            'user_id' => null,
            'valid_from' => now()->subDays(1),
            'valid_to' => null,
            'is_flash_sale' => false,
            'flash_sale_label' => null,
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

    public function flashSale(string $label = 'Flash Sale'): static
    {
        return $this->state([
            'is_flash_sale' => true,
            'flash_sale_label' => $label,
        ]);
    }
}
