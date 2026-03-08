<?php

namespace App\Filament\Widgets;

use App\Models\StockHistory;
use App\Models\TenantProductVariant;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class StockHistoryChart extends ChartWidget
{
    protected ?string $heading = 'Vývoj zásob';

    public ?TenantProductVariant $record = null;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '400px';
    public ?string $filter = 'all';

    protected string $color = 'warning';

    // mozno lepsie pie chart negativny cerveny kladny zeleny?

    protected function getData(): array
    {
        $query = $this->record->stockHistories()->orderBy('created_at');

        if ($this->filter !== 'all') {
            $query->where('created_at', '>=', Carbon::now()->subDays((int) $this->filter));
        }

        $history = $query->get();

        if ($history->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $runningTotal = 0;
        $chartData = [];
        $labels = [];

        foreach ($history as $entry) {
            $runningTotal += $entry->quantity;
            $chartData[] = $runningTotal;
            $labels[] = Carbon::parse($entry->created_at)->format('d.m.Y H:i');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Počet kusov na sklade',
                    'data' => $chartData,

                    'stepped' => false,
                    'fill' => true,
                    'tension' => 0.2,

                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
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
                'duration' => 0,
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Celá história',
            '30' => 'Posledných 30 dní',
            '90' => 'Posledné 3 mesiace',
            '365' => 'Posledný rok',
        ];
    }
}
