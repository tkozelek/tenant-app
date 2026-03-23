<?php

namespace App\Filament\Admin\Resources\TenantProductVariants\Tables;

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
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')
                    ->collection('tenant_product_variants')
                    ->label('Obrázok')
                    ->square()
                    ->limit(3),

                TextColumn::make('product.tenant.name')
                    ->label('Tenant')
                    ->visible($showTenant)
                    ->searchable()
                    ->sortable()
                    ->color('info'),

                TextColumn::make('name')
                    ->label('Nazov')
                    ->searchable()
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
                    ->getStateUsing(function (TenantProductVariant $record) {
                        return $record->variantAttributes->map(function ($pivot) {
                            $attrName = $pivot->attribute?->name ?? '??';
                            $value = $pivot->attributeValue?->value ?? $pivot->custom_value;
                            $unit = $pivot->attribute?->unit ?? '';

                            return "{$attrName}: {$value}{$unit}";
                        })->toArray();
                    })
                    ->limitList(2)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (is_array($state) && count($state) > 2) {
                            return implode(', ', $state);
                        }

                        return null;
                    }),

                TextColumn::make('price')
                    ->label('Cena')
                    ->money('EUR')
                    ->sortable()
                    ->icon(fn (TenantProductVariant $record): ?string => $record->quantityPrices->isNotEmpty() ? 'heroicon-m-rectangle-stack' : null
                    )
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
                            $formattedPrice = number_format($qp->unit_price, 2, ',', ' ');
                            $tooltipLines[] = "Od {$qp->min_quantity} ks {$maxText} -> {$formattedPrice} €";
                        }

                        return implode(', ', $tooltipLines);
                    }),

                TextColumn::make('original_price')
                    ->label('Pôvodná cena')
                    ->money('EUR')
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(TenantProductVariantExporter::class)
                        ->authorize('exportAny'),
                ]),
            ]);
    }
}
