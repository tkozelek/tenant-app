<?php

namespace App\Filament\Widgets;

use App\Models\TenantProductVariant;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PriceHistoryChart extends ChartWidget
{
    protected ?string $heading = 'Vyvoj ceny';

    // https://filamentphp.com/docs/5.x/widgets/charts

    public ?TenantProductVariant $record = null;

    protected static bool $isDiscovered = false;

    protected ?string $maxHeight = '400px';

    public ?string $filter = 'all';

    protected string $color = 'success';

    protected function getData(): array
    {
        if (!$this->record) {
            return ['datasets' => [], 'labels' => []];
        }

        $query = $this->record->priceHistories()->orderBy('valid_from');
        $startDate = null;

        if ($this->filter !== 'all') {
            $startDate = Carbon::now()->subDays((int) $this->filter);
            $query->where('valid_from', '>=', $startDate);
        }

        $histories = $query->get();
        $chartData = [];
        $labels = [];

        if ($startDate) {
            $previousPrice = $this->record->priceHistories()
                ->where('valid_from', '<', $startDate)
                ->orderByDesc('valid_from')
                ->first();

            if ($previousPrice) {
                $chartData[] = $previousPrice->price;
                $labels[] = $startDate->format('d.m.Y H:i');
            }
        }

        foreach ($histories as $history) {
            $chartData[] = $history->price;
            $labels[] = Carbon::parse($history->valid_from)->format('d.m.Y H:i');
        }

        if (!empty($chartData)) {
            $chartData[] = end($chartData);
            $labels[] = Carbon::now()->format('d.m.Y H:i');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cena (€)',
                    'data' => $chartData,

                    'stepped' => true,
                    'fill' => true,
                    'tension' => 0,
                    'responsive' => true,

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

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Celá história',
            '30' => 'Posledných 30 dní',
            '90' => 'Posledné 3 mesiace',
            '365' => 'Posledný rok',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
