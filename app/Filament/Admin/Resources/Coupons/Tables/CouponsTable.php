<?php

namespace App\Filament\Admin\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kód')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('discount_type')
                    ->label('Typ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Percento',
                        'fixed' => 'Suma',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('categories.name')
                    ->label('Kategórie')
                    ->badge()
                    ->color('gray')
                    ->limitList(2)
                    ->tooltip(function ($record) {
                        return $record->categories->pluck('name')->join(', ');
                    }),

                TextColumn::make('value')
                    ->label('Hodnota')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->discount_type === 'percentage'
                        ? "{$record->value}%"
                        : "{$record->value} €"),

                TextColumn::make('usage_limit')
                    ->label('Použitie')
                    ->formatStateUsing(fn ($record) => "{$record->used_count} / ".($record->usage_limit ?? '∞'))
                    ->sortable(['used_count', 'usage_limit']),

                TextColumn::make('starts_at')
                    ->label('Platí od')
                    ->dateTime('d. m. Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('expires_at')
                    ->label('Platí do')
                    ->dateTime('d. m. Y H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : null),

                IconColumn::make('is_active')
                    ->label('Aktívny')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Aktivny')
                    ->boolean()
                    ->placeholder('Vsetky'),

                Filter::make('expired')
                    ->label('Expirovane')
                    ->query(fn ($query) => $query->where('expires_at', '<', now()))
                    ->toggle(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
