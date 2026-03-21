<?php

namespace App\Filament\Admin\Resources\Attributes\Tables;

use App\Filament\Exports\AttributeExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AttributesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'select' => 'info',
                        'number' => 'success',
                        'bool' => 'warning',
                        'text' => 'gray',
                    }),

                TextColumn::make('unit')
                    ->searchable(),

                IconColumn::make('is_filterable')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'select' => 'Select',
                        'number' => 'Number',
                        'bool' => 'Boolean',
                        'text' => 'Text',
                    ]),
                TernaryFilter::make('is_filterable'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(AttributeExporter::class)
                        ->authorize('exportAny'),
                ]),
            ]);
    }
}
