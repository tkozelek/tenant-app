<?php

namespace App\Filament\Admin\Resources\GlobalProducts\Schemas\actions;

use App\Filament\Admin\Widgets\GlobalProductPriceHistoryChart;
use App\Models\GlobalProduct;
use Filament\Actions\Action;
use Filament\Schemas\Components\Livewire;
use Filament\Support\Enums\Width;

class HistoryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'global_product_history';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('História ceny')
            ->icon('heroicon-o-chart-bar')
            ->color('info')
            ->modalHeading(fn (GlobalProduct $record) => "História ceny: {$record->name}")
            ->modalSubmitAction(false)
            ->modalWidth(Width::SevenExtraLarge)
            ->modalCancelActionLabel('Zavrieť')
            ->schema([
                Livewire::make(GlobalProductPriceHistoryChart::class, fn (GlobalProduct $record) => [
                    'record' => $record,
                ])->columnSpanFull(),
            ]);
    }
}
