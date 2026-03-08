<?php

namespace App\Filament\Resources\TenantProducts\RelationManagers\actions;

use App\Filament\Widgets\PriceHistoryChart;
use App\Filament\Widgets\StockHistoryChart;
use App\Models\TenantProductVariant;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
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
                                RepeatableEntry::make('stockHistories')
                                    ->label('')
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Dátum')
                                            ->dateTime('d.m.Y H:i'),

                                        TextEntry::make('type')
                                            ->label('Typ')
                                            ->badge()
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'purchase' => 'Nákup',
                                                'sale' => 'Predaj',
                                                'adjustment' => 'Oprava',
                                                'return' => 'Vrátenie',
                                                'transfer' => 'Prevod',
                                                default => $state,
                                            })
                                            ->color(fn (string $state): string => match ($state) {
                                                'purchase' => 'success',
                                                'sale' => 'danger',
                                                'adjustment' => 'warning',
                                                'return' => 'info',
                                                default => 'gray',
                                            }),

                                        TextEntry::make('quantity')
                                            ->label('Množstvo')
                                            ->numeric()
                                            ->weight('bold')
                                            ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                                        TextEntry::make('user.last_name')
                                            ->limit(10)
                                            ->label('Vykonal')
                                            ->tooltip(fn ($record): string => "{$record->user?->first_name} {$record->user?->last_name} - {$record->user?->email}")
                                            ->default('-'),

                                        TextEntry::make('note')
                                            ->label('Poznámka')
                                            ->default('-'),

                                    ])
                                    ->columns(5),
                            ]),
                        Tab::make('stock_graph')
                            ->label('Sklad graf')
                            ->schema([
                                Livewire::make(StockHistoryChart::class, fn (TenantProductVariant $record) => [
                                      'record' => $record
                                ])->columnSpanFull(),
                            ]),
                        Tab::make('price')
                            ->label('Cena')
                            ->schema([
                                RepeatableEntry::make('priceHistories')
                                    ->label('')
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Dátum zmeny')
                                            ->dateTime('d.m.Y H:i'),

                                        TextEntry::make('price')
                                            ->label('Nová cena')
                                            ->money('EUR')
                                            ->weight('bold'),

                                        TextEntry::make('valid_from')
                                            ->label('Platné od')
                                            ->dateTime('d.m.Y H:i'),

                                        TextEntry::make('valid_to')
                                            ->label('Platné do')
                                            ->default('Aktuálne')
                                            ->badge(fn ($state) => $state === null),

                                        TextEntry::make('user.email')
                                            ->label('Zmenil')
                                            ->default('Systém'),
                                    ])
                                    ->columns(5),
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
