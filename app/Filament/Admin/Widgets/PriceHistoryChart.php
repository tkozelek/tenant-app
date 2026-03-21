<?php

namespace App\Filament\Admin\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class PriceHistoryChart extends ChartWidget
{
    protected ?string $heading = 'Vyvoj ceny';

    // https://filamentphp.com/docs/5.x/widgets/charts

    public ?Model $record = null;

    protected static bool $isDiscovered = false;

    protected ?string $maxHeight = '400px';

    public ?string $filter = 'all';

//    protected string $color = 'success';

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

        $priceData = [];
        $originalPriceData = [];
        $labels = [];

        if ($startDate) {
            $previousPrice = $this->record->priceHistories()
                ->where('valid_from', '<', $startDate)
                ->orderByDesc('valid_from')
                ->first();

            if ($previousPrice) {
                $priceData[] = $previousPrice->price;
                $originalPriceData[] = $previousPrice->original_price;
                $labels[] = $startDate->format('d.m.Y H:i');
            }
        }

        foreach ($histories as $history) {
            $priceData[] = $history->price;
            $originalPriceData[] = $history->original_price;
            $labels[] = Carbon::parse($history->valid_from)->format('d.m.Y H:i');
        }

        if (!empty($priceData)) {
            $priceData[] = end($priceData);
            $originalPriceData[] = end($originalPriceData);
            $labels[] = Carbon::now()->format('d.m.Y H:i');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cena (€)',
                    'data' => $priceData,

                    'stepped' => true,
                    'fill' => true,
                    'tension' => 0,
                    'responsive' => true,

                    'borderColor' => '#10b981',

                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Originalna cena (€)',
                    'data' => $originalPriceData,

                    'stepped' => true,
                    'fill' => true,
                    'tension' => 0,
                    'responsive' => true,

                    'borderColor' => '#94a3b8',
                    'borderDash' => [5, 5],

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
            'scales' => [
                'y' => [
                    'grace' => '10%'
                ]
            ]
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
