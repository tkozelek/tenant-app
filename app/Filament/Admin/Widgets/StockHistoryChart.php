<?php

namespace App\Filament\Admin\Widgets;

use App\Models\TenantProductVariant;
use App\Services\StockRecommendationService;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class StockHistoryChart extends ChartWidget
{
    protected ?string $heading = 'Vyvoj zasob';

    public ?TenantProductVariant $record = null;

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '400px';

    public ?string $filter = 'all';

    protected string $color = 'warning';

    // mozno lepsie pie chart negativny cerveny kladny zeleny?

    protected function getData(): array
    {
        if (! $this->record) {
            return [];
        }

        $query = $this->record->stockHistories()->orderBy('created_at');
        $runningTotal = 0;

        if ($this->filter !== 'all') {
            $startDate = Carbon::now()->subDays((int) $this->filter);

            $runningTotal = (int) $this->record->stockHistories()
                ->where('created_at', '<', $startDate)
                ->sum('quantity');

            $query->where('created_at', '>=', $startDate);
        }

        $history = $query->get();

        if ($history->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $chartData = [];
        $labels = [];

        foreach ($history as $entry) {
            $runningTotal += $entry->quantity;
            $chartData[] = $runningTotal;
            $labels[] = Carbon::parse($entry->created_at)->format('d.m.Y H:i');
        }

        $recommendation = app(StockRecommendationService::class)->recommend($this->record);
        $recommendedLine = array_fill(0, count($labels), $recommendation['level']);

        return [
            'datasets' => [
                [
                    'label' => 'Pocet kusov',
                    'data' => $chartData,
                    'fill' => 'origin',
                    'tension' => 0.3,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => '#6366f11f',
                    'borderWidth' => 2,
                    'pointBorderColor' => '#6366f1',
                    'pointBackgroundColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => $recommendation['description'],
                    'data' => $recommendedLine,
                    'fill' => false,
                    'tension' => 0,
                    'borderColor' => '#ef4444',
                    'borderDash' => [6, 4],
                    'borderWidth' => 1.5,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'animation' => ['duration' => 300],
            'scales' => [
                'y' => [
                    'grace' => '5%',
                    'grid' => ['color' => '#94a3b814'],
                ],
                'x' => [
                    'grid' => ['color' => '#94a3b814'],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => ['usePointStyle' => true, 'padding' => 16],
                ],
                'tooltip' => ['mode' => 'index', 'intersect' => false],
            ],
            'interaction' => ['mode' => 'nearest', 'axis' => 'x', 'intersect' => false],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Cela historia',
            '30' => 'Poslednych 30 dni',
            '90' => 'Posledne 3 mesiace',
            '365' => 'Posledny rok',
        ];
    }
}
