<?php

namespace App\Filament\Admin\Resources\TenantProductVariants\Tables;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\AdjustPriceAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\AdjustStockAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\BulkAdjustPriceAction;
use App\Filament\Exports\TenantProductVariantExporter;
use App\Models\TenantProductVariant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantProductVariantsTable
{
    public static function configure(Table $table, bool $showTenant = false): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['variantAttributes.attribute', 'variantAttributes.attributeValue', 'quantityPrices']))
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')
                    ->collection('tenant_product_variants')
                    ->label('Obrázok')
                    ->square()
                    ->limit(3),

                TextColumn::make('product.tenant.name')
                    ->label('Tenant')
                    ->visible($showTenant)
                    ->limit(15)
                    ->searchable()
                    ->sortable()
                    ->color('info'),

                TextColumn::make('name')
                    ->label('Nazov')
                    ->tooltip(fn (TenantProductVariant $record): ?string => $record->name)
                    ->searchable()
                    ->limit(15)
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('variantAttributesList')
                    ->label('Atribúty')
                    ->badge()
                    ->limitList(0)
                    ->getStateUsing(function (TenantProductVariant $record) {
                        return $record->variantAttributes->map(function ($pivot) {
                            //                            $attrName = $pivot->attribute?->name ?? '??';
                            $value = $pivot->attributeValue?->value ?? $pivot->custom_value;
                            $unit = $pivot->attribute?->unit ?? '';

                            return "{$value}{$unit}";
                        })->toArray();
                    })
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (is_array($state) && count($state) > 0) {
                            return implode(', ', $state);
                        }

                        return null;
                    }),

                TextColumn::make('current_price')
                    ->label('Cena')
                    ->state(fn (TenantProductVariant $record): string => $record->current_price_formatted)
                    ->icon(fn (TenantProductVariant $record): ?string => $record->quantityPrices->isNotEmpty() ? 'heroicon-m-rectangle-stack' : null)
                    ->iconPosition('after')
                    ->iconColor('success')
                    ->tooltip(function (TenantProductVariant $record): ?string {
                        if ($record->quantityPrices->isEmpty()) {
                            return null;
                        }

                        $prices = $record->quantityPrices->sortBy('min_quantity');
                        $tooltipLines = [];
                        foreach ($prices as $qp) {
                            $maxText = $qp->max_quantity ? "do {$qp->max_quantity} ks" : 'a viac';
                            $formattedPrice = number_format($qp->price, 2, ',', ' ');
                            $tooltipLines[] = "Od {$qp->min_quantity} ks {$maxText} -> {$formattedPrice} €";
                        }

                        return implode(', ', $tooltipLines);
                    }),

                TextColumn::make('current_original_price')
                    ->label('Pôvodná cena')
                    ->state(fn (TenantProductVariant $record): string => $record->current_original_price_formatted)
                    ->color('gray')
                    ->extraAttributes(['style' => 'text-decoration: line-through;'])
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stock_quantity')
                    ->label('KS')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 10 => 'success',
                        $state > 0 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                AdjustPriceAction::make(),
                AdjustStockAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAdjustPriceAction::make(),
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(TenantProductVariantExporter::class)
                        ->authorize('exportAny'),
                ]),
            ]);
    }
}
