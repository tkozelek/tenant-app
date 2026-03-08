<?php

namespace App\Filament\Resources\TenantProducts\RelationManagers\actions;

use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;

class StockHistoryAction extends Action
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
            ->modalCancelActionLabel('Zavrieť')
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
                    ->columns(5)
            ]);
    }
}
