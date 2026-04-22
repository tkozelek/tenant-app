<?php

namespace App\Services;

use App\Models\TenantProductVariant;
use Carbon\Carbon;

class StockRecommendationService
{
    public function recommend(
        TenantProductVariant $variant,
        int $lookbackDays = 90,
        int $leadTimeDays = 7,
        float $safetyFactor = 1.5,
    ): array {
        $since = Carbon::now()->subDays($lookbackDays);

        $totalConsumed = abs((int) $variant->stockHistories()
            ->where('created_at', '>=', $since)
            ->where('quantity', '<', 0)
            ->sum('quantity'));

        $avgDailyConsumption = $totalConsumed / $lookbackDays;

        if ($avgDailyConsumption < 0.01) {
            $level = max(1, (int) round($variant->stock_quantity * 0.1));
        } else {
            $level = (int) round($avgDailyConsumption * $leadTimeDays * $safetyFactor);
        }

        return [
            'level' => $level,
            'description' => "Odporúčané minimum (zásoby na {$leadTimeDays} dní)",
        ];
    }
}
