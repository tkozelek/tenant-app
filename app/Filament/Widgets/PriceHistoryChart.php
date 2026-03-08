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
        $query = $this->record->priceHistories()->orderBy('valid_from');

        if ($this->filter !== 'all') {
            $query->where('valid_from', '>=', Carbon::now()->subDays((int) $this->filter));
        }

        $histories = $query->get();
        if ($histories->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cena (€)',
                    'data' => $histories->pluck('price')->toArray(),

                    'stepped' => false,
                    'fill' => true,
                    'tension' => 0.2,
                    'responsive' => true,

                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $histories->pluck('valid_from')->map(fn ($date) => $date->format('d.m.Y H:i'))->toArray(),
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
