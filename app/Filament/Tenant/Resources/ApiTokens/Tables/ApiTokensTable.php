<?php

namespace App\Filament\Tenant\Resources\ApiTokens\Tables;

use App\Enums\ApiPermission;
use App\Models\ApiToken;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ApiTokensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nazov')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('abilities')
                    ->label('Pravomoci')
                    ->state(fn ($record) => collect($record->abilities)
                        ->map(fn ($ability) => ApiPermission::tryFrom($ability)?->label() ?? $ability)
                        ->toArray()
                    )
                    ->badge()
                    ->color('gray')
                    ->limitList(),

                TextColumn::make('last_used_at')
                    ->label('Posledné použitie')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('Nikdy'),

                TextColumn::make('expires_at')
                    ->label('Platí do')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color(fn (ApiToken $record): string => $record->expires_at?->isPast() ? 'danger' : 'success')
                    ->badge()
                    ->placeholder('Inf.'),

                TextColumn::make('created_at')
                    ->label('Vytvorený')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('revoke')
                    ->label('Zrušiť')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->authorize('delete')
                    ->hidden(fn (ApiToken $record): bool => $record->expires_at?->isPast() ?? false)
                    ->action(fn (ApiToken $record) => $record->update(['expires_at' => now()])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('revoke')
                        ->label('Zrušiť')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each(
                            fn (ApiToken $record) => $record->update(['expires_at' => now()])
                        ))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
