<?php

namespace App\Filament\Admin\Resources\ApiTokens\Tables;

use App\Enums\ApiPermission;
use App\Models\ApiToken;
use App\Models\Tenant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApiTokensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('tokenable.name')
                    ->label('Prevádzka')
                    ->badge()
                    ->color('info'),

                TextColumn::make('abilities')
                    ->label('Oprávnenia')
                    ->badge()
                    ->color('gray')
                    ->tooltip(fn (ApiToken $record): string => collect($record->abilities ?? [])
                        ->map(fn (string $ability): string => ApiPermission::tryFrom($ability)?->label() ?? $ability)
                        ->join("\n")
                    ),

                TextColumn::make('last_used_at')
                    ->label('Naposledy pouzity')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('Nikdy'),

                TextColumn::make('expires_at')
                    ->label('Platí do')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color(fn (ApiToken $record): string => $record->expires_at?->isPast() ? 'danger' : 'success')
                    ->badge()
                    ->placeholder('Neobmedzena'),

                TextColumn::make('createdBy.email')
                    ->label('Vytvoril')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Vytvorený')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('tokenable_id')
                    ->label('Tenant')
                    ->options(Tenant::query()->pluck('name', 'id'))
                    ->searchable()
                    ->query(fn ($query, array $data) => filled($data['value'])
                        ? $query->where('tokenable_id', $data['value'])->where('tokenable_type', Tenant::class)
                        : $query
                    ),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->label('Zrušiť')
                    ->authorize('delete'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Zrušiť'),
                ]),
            ]);
    }
}
