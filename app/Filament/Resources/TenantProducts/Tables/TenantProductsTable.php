<?php

namespace App\Filament\Resources\TenantProducts\Tables;

use App\Filament\Exports\TenantProductExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TenantProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')
                    ->collection('tenant_products')
                    ->square()
                    ->stacked()
                    ->limit(3),

                TextColumn::make('name')
                    ->weight('bold')
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('globalProduct.name')
                    ->label('Globalny produkt')
                    ->placeholder('Custom produkt')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktívny'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                SelectFilter::make('tenant_id')
                    ->searchable()
                    ->preload()
                    ->relationship('tenant', 'name')
                    ->label('Tenant'),

                SelectFilter::make('global_product_id')
                    ->searchable()
                    ->preload()
                    ->relationship('globalProduct', 'name')
                    ->label('Globalny prdukt'),

                TernaryFilter::make('is_active')
                    ->label('Je aktívny')
                    ->placeholder('-'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(TenantProductExporter::class)
                        ->authorize('exportAny'),
                ]),
            ]);
    }
}
