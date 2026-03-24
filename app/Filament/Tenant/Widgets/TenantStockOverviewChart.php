<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\TenantProductVariant;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class TenantStockOverviewChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Stav zásob — Top 10 variantov';

    protected ?string $maxHeight = '320px';

    protected string $color = 'warning';

    protected function getData(): array
    {
        $tenant = Filament::getTenant();

        $variants = TenantProductVariant::whereHas('product', fn (Builder $q) => $q->where('tenant_id', $tenant->id))
            ->with('product')
            ->orderByDesc('stock_quantity')
            ->limit(10)
            ->get();

        if ($variants->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $labels = $variants->map(fn ($v) => $v->name ?: $v->sku)->toArray();
        $data = $variants->pluck('stock_quantity')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Kusov na sklade',
                    'data' => $data,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'animation' => [
                'duration' => 300,
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grace' => '10%',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
