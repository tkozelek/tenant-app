<?php

namespace App\Filament\Admin\Resources\ActivityLogs\Tables;

use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Kedy')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                TextColumn::make('event')
                    ->label('Udalosť')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('log_name')
                    ->label('Typ')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),

                TextColumn::make('tenant.name')
                    ->default('-')
                    ->label('Tenant')
                    ->sortable(),

                TextColumn::make('causer.full_name')
                    ->label('Kým')
                    ->placeholder('Systém')
                    ->searchable(['first_name', 'last_name']),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Vytvorené',
                        'updated' => 'Zmenené',
                        'deleted' => 'Vymazané',
                    ]),

                SelectFilter::make('causer_id')
                    ->label('Používateľ')
                    ->options(fn (): array => User::query()->get()->pluck('full_name', 'id')->all())
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
