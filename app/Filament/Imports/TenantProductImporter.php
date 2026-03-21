<?php

namespace App\Filament\Imports;

use App\Models\TenantProduct;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class TenantProductImporter extends Importer
{
    protected static ?string $model = TenantProduct::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('description'),
            ImportColumn::make('is_active')
                ->boolean()
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): TenantProduct
    {
        $tenantId = $this->options['tenant_id'];

        return TenantProduct::firstOrNew([
            'tenant_id' => $tenantId,
            'name' => $this->data['name'],
        ]);
    }

    protected function beforeCreate(): void
    {
        $this->record->tenant_id = $this->options['tenant_id'];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your tenant product import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
