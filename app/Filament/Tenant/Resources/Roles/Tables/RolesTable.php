<?php

namespace App\Filament\Tenant\Resources\Roles\Tables;

use App\Filament\Exports\RoleExporter;
use App\Filament\Imports\RoleImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
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

                TextColumn::make('description')
                    ->label('Popis')
                    ->placeholder('-')
                    ->limit(60),

                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Oprávnenia')
                    ->badge()
                    ->color('info'),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Použivatelia')
                    ->badge()
                    ->color('gray'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(RoleImporter::class)
                    ->options(['tenant_scope_only' => true])
                    ->authorize('importAny'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(RoleExporter::class)
                        ->authorize('exportAny'),
                ]),
            ]);
    }
}
