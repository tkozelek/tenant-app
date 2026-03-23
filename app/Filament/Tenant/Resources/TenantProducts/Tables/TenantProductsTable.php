<?php

namespace App\Filament\Tenant\Resources\TenantProducts\Tables;

use App\Filament\Exports\TenantProductExporter;
use App\Filament\Imports\TenantProductImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
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
                    ->label('Názov')
                    ->weight('bold')
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('globalProduct.name')
                    ->label('Globálny produkt')
                    ->placeholder('Vlastný produkt')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktívny')
                    ->boolean(),

                TextColumn::make('variants_count')
                    ->label('Varianty')
                    ->counts('variants')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Vytvorené')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Je aktívny')
                    ->placeholder('-'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(TenantProductExporter::class)
                    ->modifyQueryUsing(fn ($query) => $query->where('tenant_id', Filament::getTenant()?->id)),
                ImportAction::make()
                    ->importer(TenantProductImporter::class)
                    ->options(fn () => ['tenant_id' => Filament::getTenant()?->id]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
