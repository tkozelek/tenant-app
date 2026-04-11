<?php

namespace Tests\Feature\Api\V1;

use App\Models\PriceHistory;
use App\Models\ProductQuantityPrice;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuantityPriceApiTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->token = $this->tenant->createToken('test', ['api.prices.read'])->plainTextToken;
    }

    private function variantForTenant(): TenantProductVariant
    {
        $product = TenantProduct::factory()->for($this->tenant)->create();

        return TenantProductVariant::factory()->for($product, 'product')->inStock()->create();
    }

    public function test_quantity_prices_endpoint_lists_tiers_for_variant(): void
    {
        $variant = $this->variantForTenant();

        ProductQuantityPrice::factory()->active()->create([
            'tenant_product_variant_id' => $variant->id,
            'min_quantity' => 1,
            'max_quantity' => 9,
            'price' => 10.00,
        ]);

        ProductQuantityPrice::factory()->active()->create([
            'tenant_product_variant_id' => $variant->id,
            'min_quantity' => 10,
            'max_quantity' => null,
            'price' => 8.00,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/v1/{$this->tenant->slug}/variants/{$variant->id}/quantity-prices")
            ->assertOk();

        $this->assertCount(2, $response->json('data'));
    }

    public function test_calculate_price_returns_base_price_for_quantity_1(): void
    {
        $variant = $this->variantForTenant();

        PriceHistory::factory()->active()->create([
            'tenant_product_variant_id' => $variant->id,
            'price' => 15.00,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/v1/{$this->tenant->slug}/variants/{$variant->id}/calculate-price?quantity=1")
            ->assertOk();

        $this->assertEquals(1, $response->json('quantity'));
        $this->assertEquals(15.00, $response->json('unit_price'));
        $this->assertEquals(15.00, $response->json('total_price'));
        $this->assertFalse($response->json('quantity_tier_applied'));
    }

    public function test_calculate_price_applies_quantity_tier(): void
    {
        $variant = $this->variantForTenant();

        PriceHistory::factory()->active()->create([
            'tenant_product_variant_id' => $variant->id,
            'price' => 10.00,
        ]);

        ProductQuantityPrice::factory()->active()->create([
            'tenant_product_variant_id' => $variant->id,
            'min_quantity' => 10,
            'max_quantity' => null,
            'price' => 8.00,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/v1/{$this->tenant->slug}/variants/{$variant->id}/calculate-price?quantity=10")
            ->assertOk();

        $this->assertEquals(8.00, $response->json('unit_price'));
        $this->assertEquals(80.00, $response->json('total_price'));
        $this->assertTrue($response->json('quantity_tier_applied'));
    }

    public function test_calculate_price_skips_tier_during_flash_sale(): void
    {
        $variant = $this->variantForTenant();

        PriceHistory::factory()->active()->flashSale()->create([
            'tenant_product_variant_id' => $variant->id,
            'price' => 5.00,
        ]);

        ProductQuantityPrice::factory()->active()->create([
            'tenant_product_variant_id' => $variant->id,
            'min_quantity' => 10,
            'max_quantity' => null,
            'price' => 4.00,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/v1/{$this->tenant->slug}/variants/{$variant->id}/calculate-price?quantity=10")
            ->assertOk();

        $this->assertFalse($response->json('quantity_tier_applied'));
        $this->assertEquals(5.00, $response->json('unit_price'));
    }

    public function test_calculate_price_returns_422_for_quantity_zero(): void
    {
        $variant = $this->variantForTenant();

        $this->withToken($this->token)
            ->getJson("/api/v1/{$this->tenant->slug}/variants/{$variant->id}/calculate-price?quantity=0")
            ->assertStatus(422);
    }

    public function test_calculate_price_returns_404_for_variant_of_another_tenant(): void
    {
        $otherTenant = Tenant::factory()->create();
        $product = TenantProduct::factory()->for($otherTenant)->create();
        $variant = TenantProductVariant::factory()->for($product, 'product')->create();

        $this->withToken($this->token)
            ->getJson("/api/v1/{$this->tenant->slug}/variants/{$variant->id}/calculate-price?quantity=1")
            ->assertNotFound();
    }
}
