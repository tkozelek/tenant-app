<?php

namespace App\Filament\Admin\Widgets;

use App\Services\PricePredictionService;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Database\Eloquent\Model;

class PriceHistoryChart extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Vyvoj ceny';

    // https://filamentphp.com/docs/5.x/widgets/charts

    public ?Model $record = null;

    protected static bool $isDiscovered = false;

    protected ?string $maxHeight = '400px';

    //    protected string $color = 'success';

    protected function getData(): array
    {
        if (! $this->record) {
            return ['datasets' => [], 'labels' => []];
        }

        $predictionService = app(PricePredictionService::class);

        $query = $this->record->priceHistories()->orderBy('valid_from');
        $startDate = null;

        $history = $this->filters['history'] ?? 'all';
        // date filter
        if ($history !== 'all') {
            $startDate = Carbon::now()->subDays((int) $history);
            $query->where('valid_from', '>=', $startDate);
        }

        $histories = $query->get();

        $priceData = [];
        $originalPriceData = [];
        $labels = [];

        // ak je date filter, nech neoreze zaciatok
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

        if (! empty($priceData)) {
            $priceData[] = end($priceData);
            $originalPriceData[] = end($originalPriceData);
            $labels[] = Carbon::now()->format('d.m.Y H:i');
        }
        $predictionDataset = [];
        if (count($priceData) > 1) {
            // predikcia - dlzka sa berie z filtersSchema selectu, max 90 dni
            $predictionDays = min(90, max(14, (int) ($this->filters['prediction_days'] ?? 60)));
            $prediction = $predictionService->predict($this->record, $predictionDays);

            $predictionDataset = array_fill(0, count($labels) - 1, null);
            $predictionDataset[] = ! empty($priceData) ? (float) end($priceData) : null;

            foreach ($prediction['values'] as $value) {
                $priceData[] = null;
                $originalPriceData[] = null;
                $predictionDataset[] = $value;
            }

            $labels = array_merge($labels, $prediction['labels']);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cena (€)',
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
                    'label' => 'Orig. cena (€)',
                    'data' => $originalPriceData,
                    'stepped' => true,
                    'fill' => false,
                    'tension' => 0,
                    'borderColor' => '#94a3b8',
                    'borderDash' => [5, 5],
                    'borderWidth' => 1.5,
                    'pointBorderColor' => '#94a3b8',
                    'pointBackgroundColor' => '#ffffff',
                    'pointBorderWidth' => 1.5,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
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
                ->label('Dlzka predikcie')
                ->options([
                    '14' => '14 dni',
                    '30' => '30 dni',
                    '60' => '60 dni',
                    '90' => '90 dni',
                ])
                ->default('14')
                ->selectablePlaceholder(false),

            Select::make('history')
                ->label('Historia cien')
                ->options([
                    'all' => 'Cela historia',
                    '30' => 'Poslednych 30 dni',
                    '90' => 'Posledne 3 mesiace',
                    '180' => 'Posledne 6 mesiace',
                    '365' => 'Posledny rok',
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
