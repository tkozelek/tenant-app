<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discountType = fake()->randomElement(['fixed', 'percentage']);

        $usageLimit = fake()->boolean(90) ? fake()->numberBetween(10, 500) : null;
        $isUsed = fake()->optional(0.7);

        return [
            'tenant_id' => Tenant::factory(),
            'code' => strtoupper(fake()->unique()->bothify('????-####')),
            'description' => fake()->sentence(),
            'discount_type' => $discountType,
            'value' => $discountType === 'percentage'
                ? fake()->numberBetween(5, 50)
                : fake()->numberBetween(5, 100),
            'min_order_amount' => fake()->optional(0.7)->numberBetween(20, 200),
            'usage_limit' => $usageLimit,
            'used_count' => $usageLimit && $isUsed ? fake()->numberBetween(0, $usageLimit) : 0,
            'starts_at' => now()->subDays(fake()->numberBetween(1, 30)),
            'expires_at' => now()->addDays(fake()->numberBetween(7, 90)),
            'is_active' => fake()->boolean(90),
        ];
    }
}
