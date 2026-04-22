<?php

namespace Tests\Unit;

use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_valid_returns_true_for_active_in_date_range(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(7),
            'usage_limit' => null,
            'used_count' => 0,
        ]);

        $this->assertTrue($coupon->isValid());
    }

    public function test_is_valid_returns_false_when_inactive(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active' => false,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(7),
        ]);

        $this->assertFalse($coupon->isValid());
    }

    public function test_is_valid_returns_false_when_not_yet_started(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active' => true,
            'starts_at' => now()->addDay(),
            'expires_at' => now()->addDays(7),
        ]);

        $this->assertFalse($coupon->isValid());
    }

    public function test_is_valid_returns_false_when_expired(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active' => true,
            'starts_at' => now()->subDays(7),
            'expires_at' => now()->subDay(),
        ]);

        $this->assertFalse($coupon->isValid());
    }

    public function test_is_valid_returns_false_when_usage_limit_reached(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(7),
            'usage_limit' => 10,
            'used_count' => 10,
        ]);

        $this->assertFalse($coupon->isValid());
    }

    public function test_is_valid_returns_true_when_usage_limit_not_yet_reached(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(7),
            'usage_limit' => 10,
            'used_count' => 9,
        ]);

        $this->assertTrue($coupon->isValid());
    }

    public function test_is_valid_returns_true_with_null_usage_limit(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(7),
            'usage_limit' => null,
            'used_count' => 999,
        ]);

        $this->assertTrue($coupon->isValid());
    }
}
