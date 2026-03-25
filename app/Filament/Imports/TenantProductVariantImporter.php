<?php

namespace App\Filament\Imports;

use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class TenantProductVariantImporter extends Importer
{
    protected static ?string $model = TenantProductVariant::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('tenant_product_id')
                ->label('Produkt (podľa názvu)')
                ->requiredMapping()
                ->rules(['required'])
                ->castStateUsing(function (string $state, array $options): ?int {
                    return TenantProduct::where('tenant_id', $options['tenant_id'])
                        ->where('name', $state)
                        ->value('id');
                }),
            ImportColumn::make('name')
                ->rules(['max:255']),
            ImportColumn::make('sku')
                ->label('SKU')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('ean')
                ->rules(['max:50']),
            ImportColumn::make('stock_quantity')
                ->numeric()
                ->rules(['integer']),
        ];
    }

    public function resolveRecord(): TenantProductVariant
    {
        return TenantProductVariant::firstOrNew([
            'sku' => $this->data['sku'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your tenant product variant import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
