<?php

namespace App\Filament\Exports;

use App\Models\TenantProductVariant;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class TenantProductVariantExporter extends Exporter
{
    protected static ?string $model = TenantProductVariant::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('tenant_product_id'),
            ExportColumn::make('name'),
            ExportColumn::make('sku')
                ->label('SKU'),
            ExportColumn::make('ean'),
            ExportColumn::make('price'),
            ExportColumn::make('original_price'),
            ExportColumn::make('stock_quantity'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your tenant product variant export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
