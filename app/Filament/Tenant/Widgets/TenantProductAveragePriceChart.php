<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\PriceHistory;
use App\Models\TenantProduct;
use App\Services\PricePredictionService;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class TenantProductAveragePriceChart extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Average vyvoj ceny tenant produktu';

    public ?TenantProduct $record = null;

    protected static bool $isDiscovered = false;

    protected ?string $maxHeight = '400px';

    protected function getData(): array
    {
        if (! $this->record) {
            return ['datasets' => [], 'labels' => []];
        }

        $variantIds = $this->record->variants()->pluck('id');

        if ($variantIds->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $query = PriceHistory::whereIn('tenant_product_variant_id', $variantIds)
            ->orderBy('valid_from');

        $history = $this->filters['history'] ?? 'all';
        if ($history !== 'all') {
            $startDate = Carbon::now()->subDays((int) $history);
            $query->where('valid_from', '>=', $startDate);
        }

        $allHistories = PriceHistory::whereIn('tenant_product_variant_id', $variantIds)
            ->orderBy('valid_from')
            ->get()
            ->groupBy('tenant_product_variant_id');

        $timestamps = $query->pluck('valid_from')
            ->unique()
            ->sort()
            ->values();

        if ($timestamps->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $priceData = [];
        $labels = [];

        foreach ($timestamps as $ts) {
            $prices = [];

            foreach ($variantIds as $variantId) {
                $variantHistories = $allHistories->get($variantId, collect());
                $activeHistory = $variantHistories
                    ->filter(fn ($h) => Carbon::parse($h->valid_from)->lessThanOrEqualTo(Carbon::parse($ts)))
                    ->last();

                if ($activeHistory) {
                    $prices[] = (float) $activeHistory->price;
                }
            }

            if (! empty($prices)) {
                $priceData[] = round(array_sum($prices) / count($prices), 2);
                $labels[] = Carbon::parse($ts)->format('d.m.Y H:i');
            }
        }

        if (! empty($priceData)) {
            $priceData[] = end($priceData);
            $labels[] = Carbon::now()->format('d.m.Y H:i');
        }

        $predictionDataset = [];
        if (count($priceData) > 1) {
            $predictionDays = min(90, max(14, (int) ($this->filters['prediction_days'] ?? 60)));
            $prediction = app(PricePredictionService::class)->predict($this->record, $predictionDays);

            $predictionDataset = array_fill(0, count($labels) - 1, null);
            $predictionDataset[] = ! empty($priceData) ? (float) end($priceData) : null;

            foreach ($prediction['values'] as $value) {
                $priceData[] = null;
                $predictionDataset[] = $value;
            }

            $labels = array_merge($labels, $prediction['labels']);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Priem. cena (€)',
                    'data' => $priceData,
                    'stepped' => true,
                    'fill' => 'origin',
                    'tension' => 0,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => '#6366f112',
                    'borderWidth' => 2,
                    'pointBorderColor' => '#6366f1',
                    'pointBackgroundColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'spanGaps' => false,
                ],
                [
                    'label' => 'Predikcia (€)',
                    'data' => $predictionDataset,
                    'stepped' => false,
                    'fill' => false,
                    'tension' => 0.4,
                    'borderColor' => '#f59e0b',
                    'borderDash' => [8, 4],
                    'borderWidth' => 2,
                    'pointBorderColor' => '#f59e0b',
                    'pointBackgroundColor' => '#f59e0b',
                    'pointBorderWidth' => 1,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                    'spanGaps' => false,
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
                    'grace' => '10%',
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

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('prediction_days')
                ->label('Dĺžka predikcie')
                ->options([
                    '14' => '14 dní',
                    '30' => '30 dní',
                    '60' => '60 dní',
                    '90' => '90 dní',
                ])
                ->default('60')
                ->selectablePlaceholder(false),

            Select::make('history')
                ->label('História cien')
                ->options([
                    'all' => 'Celá história',
                    '30' => 'Posledných 30 dní',
                    '90' => 'Posledné 3 mesiace',
                    '180' => 'Posledných 6 mesiacov',
                    '365' => 'Posledný rok',
                ])
                ->default('all')
                ->selectablePlaceholder(false),
        ]);
    }

    protected function getType(): string
    {
        return 'line';
    }
}
