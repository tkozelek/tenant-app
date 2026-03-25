<?php

namespace App\Filament\Admin\Resources\Roles\Tables;

use App\Filament\Exports\RoleExporter;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->badge()
                    ->color('info')
                    ->placeholder('Global'),

                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Počet povolení')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('tenant')
                    ->label('Tenant')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(RoleExporter::class)
                    ->authorize('exportAny'),
            ]);
    }
}
