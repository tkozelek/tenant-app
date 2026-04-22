<?php

namespace App\Filament\Tenant\Resources\Bundles\Tables;

use App\Filament\Admin\Resources\Bundles\Schemas\actions\HistoryAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BundlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('activePriceHistory.price')
                    ->label('Cena')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Počet položiek')
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_active')
                    ->label('Aktívny')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Vytvorené')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                HistoryAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
