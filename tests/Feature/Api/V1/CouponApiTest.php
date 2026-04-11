<?php

namespace Tests\Feature\Api\V1;

use App\Models\Coupon;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponApiTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private string $readToken;

    private string $writeToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->readToken = $this->tenant->createToken('read', ['api.coupons.read'])->plainTextToken;
        $this->writeToken = $this->tenant->createToken('write', ['api.coupons.write'])->plainTextToken;
    }

    private function activeCoupon(array $overrides = []): Coupon
    {
        return Coupon::factory()->create(array_merge([
            'tenant_id' => $this->tenant->id,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(7),
            'usage_limit' => null,
            'used_count' => 0,
            'min_order_amount' => null,
        ], $overrides));
    }

    public function test_index_returns_coupons_for_tenant(): void
    {
        $coupon = $this->activeCoupon();
        $otherCoupon = Coupon::factory()->create(['tenant_id' => Tenant::factory()->create()->id]);

        $response = $this->withToken($this->readToken)
            ->getJson("/api/v1/{$this->tenant->slug}/coupons")
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($coupon->id, $ids);
        $this->assertNotContains($otherCoupon->id, $ids);
    }

    public function test_check_returns_valid_for_active_coupon(): void
    {
        $coupon = $this->activeCoupon();

        $this->withToken($this->readToken)
            ->postJson("/api/v1/{$this->tenant->slug}/coupons/check", ['code' => $coupon->code])
            ->assertOk()
            ->assertJsonPath('valid', true);
    }

    public function test_check_returns_404_for_unknown_code(): void
    {
        $this->withToken($this->readToken)
            ->postJson("/api/v1/{$this->tenant->slug}/coupons/check", ['code' => 'NONEXISTENT'])
            ->assertNotFound()
            ->assertJsonPath('valid', false);
    }

    public function test_check_returns_invalid_for_inactive_coupon(): void
    {
        $coupon = $this->activeCoupon(['is_active' => false]);

        $this->withToken($this->readToken)
            ->postJson("/api/v1/{$this->tenant->slug}/coupons/check", ['code' => $coupon->code])
            ->assertOk()
            ->assertJsonPath('valid', false);
    }

    public function test_check_returns_invalid_when_usage_limit_reached(): void
    {
        $coupon = $this->activeCoupon(['usage_limit' => 5, 'used_count' => 5]);

        $this->withToken($this->readToken)
            ->postJson("/api/v1/{$this->tenant->slug}/coupons/check", ['code' => $coupon->code])
            ->assertOk()
            ->assertJsonPath('valid', false);
    }

    public function test_check_returns_invalid_when_order_amount_too_low(): void
    {
        $coupon = $this->activeCoupon(['min_order_amount' => 100.00]);

        $this->withToken($this->readToken)
            ->postJson("/api/v1/{$this->tenant->slug}/coupons/check", [
                'code' => $coupon->code,
                'order_amount' => 50.00,
            ])
            ->assertOk()
            ->assertJsonPath('valid', false);
    }

    public function test_use_increments_used_count(): void
    {
        $coupon = $this->activeCoupon(['used_count' => 0]);

        $this->withToken($this->writeToken)
            ->postJson("/api/v1/{$this->tenant->slug}/coupons/use", ['code' => $coupon->code])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertEquals(1, $coupon->fresh()->used_count);
    }

    public function test_use_fails_for_expired_coupon(): void
    {
        $coupon = $this->activeCoupon(['expires_at' => now()->subDay()]);

        $this->withToken($this->writeToken)
            ->postJson("/api/v1/{$this->tenant->slug}/coupons/use", ['code' => $coupon->code])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_use_requires_write_permission(): void
    {
        $coupon = $this->activeCoupon();

        $this->withToken($this->readToken)
            ->postJson("/api/v1/{$this->tenant->slug}/coupons/use", ['code' => $coupon->code])
            ->assertStatus(403);
    }
}
