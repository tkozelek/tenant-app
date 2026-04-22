<?php

namespace Tests\Unit;

use App\Models\PriceHistory;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariantComputedAttributesTest extends TestCase
{
    use RefreshDatabase;

    private TenantProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::factory()->create();
        $product = TenantProduct::factory()->for($tenant)->create();
        $this->variant = TenantProductVariant::factory()->for($product, 'product')->create();
    }

    public function test_current_price_is_null_when_no_active_price_history(): void
    {
        $this->variant->load('activePriceHistory');

        $this->assertNull($this->variant->current_price);
    }

    public function test_current_price_returns_float_from_active_price_history(): void
    {
        PriceHistory::factory()->active()->create([
            'tenant_product_variant_id' => $this->variant->id,
            'price' => 49.99,
        ]);

        $this->variant->load('activePriceHistory');

        $this->assertEquals(49.99, $this->variant->current_price);
        $this->assertIsFloat($this->variant->current_price);
    }

    public function test_expired_price_history_does_not_count_as_current_price(): void
    {
        PriceHistory::factory()->expired()->create([
            'tenant_product_variant_id' => $this->variant->id,
            'price' => 99.99,
        ]);

        $this->variant->load('activePriceHistory');

        $this->assertNull($this->variant->current_price);
    }

    public function test_current_original_price_returns_float_when_set(): void
    {
        PriceHistory::factory()->active()->create([
            'tenant_product_variant_id' => $this->variant->id,
            'price' => 49.99,
            'original_price' => 79.99,
        ]);

        $this->variant->load('activePriceHistory');

        $this->assertEquals(79.99, $this->variant->current_original_price);
        $this->assertIsFloat($this->variant->current_original_price);
    }

    public function test_most_recent_active_price_history_wins(): void
    {
        PriceHistory::factory()->create([
            'tenant_product_variant_id' => $this->variant->id,
            'price' => 30.00,
            'valid_from' => now()->subDays(10),
            'valid_to' => null,
        ]);

        PriceHistory::factory()->create([
            'tenant_product_variant_id' => $this->variant->id,
            'price' => 55.00,
            'valid_from' => now()->subDays(2),
            'valid_to' => null,
        ]);

        $this->variant->load('activePriceHistory');

        $this->assertEquals(55.00, $this->variant->current_price);
    }
}
