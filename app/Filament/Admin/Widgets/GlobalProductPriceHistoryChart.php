<?php

namespace App\Filament\Admin\Widgets;

use App\Models\GlobalProduct;
use App\Models\PriceHistory;
use App\Services\PricePredictionService;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class GlobalProductPriceHistoryChart extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Vývoj ceny pod produktov';

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '400px';

    protected int|string|array $columnSpan = 'full';

    public ?GlobalProduct $record = null;

    protected function getData(): array
    {
        if (! $this->record) {
            return ['datasets' => [], 'labels' => []];
        }

        $variantIds = $this->record->variants()->pluck('tenant_product_variants.id');

        if ($variantIds->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $period = $this->filters['period'] ?? 'all';
        $isLongPeriod = in_array($period, ['365', 'all']);

        $baseQuery = PriceHistory::query()
            ->whereIn('tenant_product_variant_id', $variantIds);

        if ($period !== 'all') {
            $baseQuery->where('valid_from', '>=', Carbon::now()->subDays((int) $period));
        }

        // Aggregate at DB level to avoid loading individual rows
        $dailyAggregates = (clone $baseQuery)
            ->selectRaw('DATE(valid_from) as date, AVG(price) as avg_price')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($dailyAggregates->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $predictionService = app(PricePredictionService::class);

        $grouped = $dailyAggregates
            ->groupBy(fn ($row) => $isLongPeriod
                ? Carbon::parse($row->date)->startOfWeek()->format('d.m.Y')
                : Carbon::parse($row->date)->format('d.m.Y')
            )
            ->map(fn ($group) => round($group->avg('avg_price'), 2));

        $labels = $grouped->keys()->toArray();
        $priceData = $grouped->values()->toArray();

        if (! empty($priceData)) {
            $priceData[] = end($priceData);
            $labels[] = Carbon::now()->format('d.m.Y');
        }

        // For prediction: use most recent 500 days of full history, aggregated at DB level
        $allHistories = PriceHistory::query()
            ->selectRaw('DATE(valid_from) as date, AVG(price) as avg_price')
            ->whereIn('tenant_product_variant_id', $variantIds)
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(500)
            ->get()
            ->sortBy('date')
            ->values()
            ->map(fn ($row) => [
                'timestamp' => Carbon::parse($row->date)->startOfWeek()->unix(),
                'price' => round((float) $row->avg_price, 2),
            ])
            ->toArray();

        $predictionDays = min(90, max(14, (int) ($this->filters['prediction_days'] ?? 60)));
        $prediction = $predictionService->predictFromDataPoints($allHistories, $predictionDays);

        // chartjs mapovanie hodnot.. treba spravit array rovnakej dlzky + nech pokracuje.. idk why
        $predictionDataset = array_fill(0, count($labels) - 1, null);
        $predictionDataset[] = ! empty($priceData) ? (float) end($priceData) : null;

        foreach ($prediction['values'] as $value) {
            $priceData[] = null;
            $predictionDataset[] = $value;
        }

        $labels = array_merge($labels, $prediction['labels']);

        return [
            'datasets' => [
                [
                    'label' => 'Priemerná cena (€)',
                    'data' => $priceData,
                    'stepped' => true,
                    'fill' => 'origin',
                    'tension' => 0,
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b98112',
                    'borderWidth' => 2,
                    'pointBorderColor' => '#10b981',
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
            Select::make('period')
                ->label('Obdobie')
                ->options([
                    'all' => 'Cela historia',
                    '30' => 'Poslednych 30 dno',
                    '90' => 'Posledne 3 mesiace',
                    '365' => 'Posledny rok',
                ])
                ->default('all')
                ->selectablePlaceholder(false),

            Select::make('prediction_days')
                ->label('Dĺžka predikcie')
                ->options([
                    '14' => '14 dni',
                    '30' => '30 dni',
                    '60' => '60 dni',
                    '90' => '90 dni',
                ])
                ->default('30')
                ->selectablePlaceholder(false),
        ]);
    }

    protected function getType(): string
    {
        return 'line';
    }
}
