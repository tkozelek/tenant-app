<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use App\Services\StockRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockRecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockRecommendationService $service;

    private TenantProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StockRecommendationService;

        $tenant = Tenant::factory()->create();
        $product = TenantProduct::factory()->for($tenant)->create();
        $this->variant = TenantProductVariant::factory()->for($product, 'product')->inStock(100)->create();
    }

    private function addConsumption(int $quantity, ?string $createdAt = null): void
    {
        $record = $this->variant->stockHistories()->make([
            'type' => 'sale',
            'quantity' => -abs($quantity),
            'note' => null,
            'user_id' => null,
        ]);

        $record->forceFill([
            'created_at' => $createdAt ?? now(),
            'updated_at' => $createdAt ?? now(),
        ])->save();
    }

    public function test_no_consumption_falls_back_to_10_percent_of_stock(): void
    {
        $result = $this->service->recommend($this->variant);

        $this->assertEquals(10, $result['level']);
    }

    public function test_very_low_stock_fallback_is_at_least_1(): void
    {
        $variant = TenantProductVariant::factory()
            ->for($this->variant->product, 'product')
            ->inStock(3)
            ->create();

        $result = $this->service->recommend($variant);

        $this->assertEquals(1, $result['level']);
    }

    public function test_consumption_history_drives_recommendation(): void
    {
        for ($i = 0; $i < 9; $i++) {
            $this->addConsumption(10, now()->subDays($i * 9)->toDateTimeString());
        }

        $result = $this->service->recommend($this->variant, lookbackDays: 90, leadTimeDays: 7, safetyFactor: 1.5);

        $this->assertEquals(11, $result['level']);
    }

    public function test_custom_lead_time_and_safety_factor_are_applied(): void
    {
        for ($i = 0; $i < 9; $i++) {
            $this->addConsumption(10, now()->subDays($i * 9)->toDateTimeString());
        }

        $result = $this->service->recommend($this->variant, lookbackDays: 90, leadTimeDays: 14, safetyFactor: 2.0);

        $this->assertEquals(28, $result['level']);
    }

    public function test_consumption_outside_lookback_window_is_ignored(): void
    {
        $this->addConsumption(500, now()->subDays(180)->toDateTimeString());

        $result = $this->service->recommend($this->variant, lookbackDays: 90);

        $this->assertEquals(10, $result['level']);
    }

    public function test_only_negative_stock_movements_count_as_consumption(): void
    {
        $this->variant->stockHistories()->create([
            'type' => 'purchase',
            'quantity' => 1000,
            'note' => null,
            'user_id' => null,
        ]);

        $result = $this->service->recommend($this->variant);

        $this->assertEquals(10, $result['level']);
    }
}
