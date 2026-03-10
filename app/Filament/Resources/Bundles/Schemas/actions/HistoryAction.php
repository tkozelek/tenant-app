<?php

namespace App\Filament\Resources\Bundles\Schemas\actions;

use App\Filament\Tables\PriceHistoryTable;
use App\Filament\Widgets\PriceHistoryChart;
use App\Models\Bundle;
use Filament\Actions\Action;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;

class HistoryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'bundles_history';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('História')
            ->icon('heroicon-o-clock')
            ->color('info')
            ->modalHeading(fn ($record) => "História: {$record->name}")
            ->modalSubmitAction(false)
            ->modalWidth(Width::SevenExtraLarge)
            ->modalCancelActionLabel('Zavrieť')
            ->schema([
                Tabs::make('history')
                    ->label('História')
                    ->tabs([
                        Tab::make('price')
                            ->label('Cena')
                            ->schema([
                                Livewire::make(PriceHistoryTable::class, fn (Bundle $record) => [
                                    'record' => $record,
                                ])->columnSpanFull(),
                            ]),
                        Tab::make('price_graph')
                            ->label('Cena graf')
                            ->schema([
                                Livewire::make(PriceHistoryChart::class, fn (Bundle $record) => [
                                    'record' => $record,
                                ]),
                            ]),
                    ]),
            ]);
    }
}
