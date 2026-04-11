<?php

namespace Tests\Feature\Api\V1;

use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockApiTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private TenantProductVariant $variant;

    private string $writeToken;

    private string $readToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->writeToken = $this->tenant->createToken('write', ['api.stock.write'])->plainTextToken;
        $this->readToken = $this->tenant->createToken('read', ['api.stock.read'])->plainTextToken;

        $product = TenantProduct::factory()->for($this->tenant)->create();
        $this->variant = TenantProductVariant::factory()->for($product, 'product')->inStock(20)->create();
    }

    public function test_adjusting_stock_increases_quantity_and_records_history(): void
    {
        $this->withToken($this->writeToken)
            ->postJson("/api/v1/{$this->tenant->slug}/variants/{$this->variant->id}/stock", [
                'type' => 'purchase',
                'quantity' => 10,
                'note' => 'Restock',
            ])
            ->assertOk()
            ->assertJsonPath('stock_quantity', 30);

        $this->assertEquals(30, $this->variant->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_history', [
            'product_variant_id' => $this->variant->id,
            'type' => 'purchase',
            'quantity' => 10,
            'note' => 'Restock',
        ]);
    }

    public function test_adjusting_stock_with_negative_quantity_decreases_stock(): void
    {
        $this->withToken($this->writeToken)
            ->postJson("/api/v1/{$this->tenant->slug}/variants/{$this->variant->id}/stock", [
                'type' => 'sale',
                'quantity' => -5,
            ])
            ->assertOk()
            ->assertJsonPath('stock_quantity', 15);

        $this->assertEquals(15, $this->variant->fresh()->stock_quantity);
    }

    public function test_stock_adjustment_requires_write_permission(): void
    {
        $this->withToken($this->readToken)
            ->postJson("/api/v1/{$this->tenant->slug}/variants/{$this->variant->id}/stock", [
                'type' => 'purchase',
                'quantity' => 5,
            ])
            ->assertStatus(403);
    }

    public function test_stock_adjustment_returns_404_for_variant_of_another_tenant(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherProduct = TenantProduct::factory()->for($otherTenant)->create();
        $otherVariant = TenantProductVariant::factory()->for($otherProduct, 'product')->create();

        $this->withToken($this->writeToken)
            ->postJson("/api/v1/{$this->tenant->slug}/variants/{$otherVariant->id}/stock", [
                'type' => 'purchase',
                'quantity' => 5,
            ])
            ->assertNotFound();
    }
}
