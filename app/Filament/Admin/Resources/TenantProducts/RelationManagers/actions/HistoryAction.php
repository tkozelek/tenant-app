<?php

namespace App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions;

use App\Filament\Admin\Widgets\PriceHistoryChart;
use App\Filament\Admin\Widgets\StockHistoryChart;
use App\Filament\Tables\PriceHistoryTable;
use App\Filament\Tables\StockHistoryTable;
use App\Models\TenantProductVariant;
use Filament\Actions\Action;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;

class HistoryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'view_stock_history';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('História')
            ->icon('heroicon-o-clock')
            ->color('info')
            ->modalHeading(fn ($record) => "História skladu: {$record->name}")
            ->modalSubmitAction(false)
            ->modalWidth(Width::SevenExtraLarge)
            ->modalCancelActionLabel('Zavrieť')
            ->schema([
                Tabs::make('history')
                    ->label('História')
                    ->tabs([
                        Tab::make('stock')
                            ->label('Sklad')
                            ->schema([
                                Livewire::make(StockHistoryTable::class, fn (TenantProductVariant $record) => [
                                    'record' => $record,
                                ])->columnSpanFull(),
                            ]),
                        Tab::make('stock_graph')
                            ->label('Sklad graf')
                            ->schema([
                                Livewire::make(StockHistoryChart::class, fn (TenantProductVariant $record) => [
                                    'record' => $record,
                                ])->columnSpanFull(),
                            ]),
                        Tab::make('price')
                            ->label('Cena')
                            ->schema([
                                Livewire::make(PriceHistoryTable::class, fn (TenantProductVariant $record) => [
                                    'record' => $record,
                                ])->columnSpanFull(),
                            ]),
                        Tab::make('price_graph')
                            ->label('Cena graf')
                            ->schema([
                                Livewire::make(PriceHistoryChart::class, fn (TenantProductVariant $record) => [
                                    'record' => $record,
                                ]),
                            ]),
                    ]),
            ]);
    }
}
