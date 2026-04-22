<?php

namespace Tests\Unit;

use App\Services\PricePredictionService;
use Carbon\Carbon;
use Tests\TestCase;

class PricePredictionServiceTest extends TestCase
{
    private PricePredictionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PricePredictionService;
    }

    private function makePoints(array $prices): array
    {
        $points = [];
        $base = Carbon::now()->subDays(count($prices) * 30);

        foreach ($prices as $i => $price) {
            $points[] = [
                'timestamp' => $base->copy()->addDays($i * 30)->unix(),
                'price' => $price,
            ];
        }

        return $points;
    }

    public function test_returns_correct_number_of_prediction_points(): void
    {
        $result = $this->service->predictFromDataPoints($this->makePoints([10.0, 20.0, 30.0]), 60);

        $this->assertCount(9, $result['labels']);
        $this->assertCount(9, $result['values']);
    }

    public function test_ascending_trend_produces_increasing_predictions(): void
    {
        $result = $this->service->predictFromDataPoints(
            $this->makePoints([10.0, 20.0, 30.0, 40.0, 50.0])
        );

        $values = $result['values'];
        $this->assertGreaterThan(50.0, $values[0]);

        for ($i = 1; $i < count($values); $i++) {
            $this->assertGreaterThanOrEqual($values[$i - 1], $values[$i]);
        }
    }

    public function test_descending_trend_produces_decreasing_predictions(): void
    {
        $result = $this->service->predictFromDataPoints(
            $this->makePoints([100.0, 80.0, 60.0, 40.0, 20.0])
        );

        $values = $result['values'];
        $this->assertLessThan(20.0, $values[0]);

        for ($i = 1; $i < count($values); $i++) {
            $this->assertLessThanOrEqual($values[$i - 1], $values[$i]);
        }
    }

    public function test_flat_trend_produces_stable_predictions(): void
    {
        $result = $this->service->predictFromDataPoints(
            $this->makePoints([50.0, 50.0, 50.0, 50.0, 50.0])
        );

        foreach ($result['values'] as $value) {
            $this->assertEqualsWithDelta(50.0, $value, 0.5);
        }
    }

    public function test_predicted_values_are_never_negative(): void
    {
        $result = $this->service->predictFromDataPoints(
            $this->makePoints([500.0, 100.0, 10.0, 2.0, 0.5])
        );

        foreach ($result['values'] as $value) {
            $this->assertGreaterThanOrEqual(0.0, $value);
        }
    }

    public function test_more_days_ahead_produces_more_prediction_points(): void
    {
        $points = $this->makePoints([10.0, 20.0, 30.0]);

        $result14 = $this->service->predictFromDataPoints($points, 14);
        $this->assertCount(4, $result14['labels']);

        $result70 = $this->service->predictFromDataPoints($points, 70);
        $this->assertCount(10, $result70['labels']);
    }
}
