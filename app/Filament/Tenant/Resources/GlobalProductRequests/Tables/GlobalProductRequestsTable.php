<?php

namespace App\Filament\Tenant\Resources\GlobalProductRequests\Tables;

use App\Models\GlobalProductRequest;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GlobalProductRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('suggested_name')
                    ->label('Názov produktu')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('suggestedCategory.name')
                    ->label('Kategória')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Čaká',
                        'approved' => 'Schválená',
                        'rejected' => 'Zamietnutá',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Dátum')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->placeholder('Všetky')
                    ->options([
                        'pending' => 'Čaká',
                        'approved' => 'Schválená',
                        'rejected' => 'Zamietnutá',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(fn (GlobalProductRequest $record): string => $record->status === 'pending' ? 'Upraviť' : 'Zobraziť'),
            ]);
    }
}
