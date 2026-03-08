<?php

namespace App\Filament\Exports;

use App\Models\GlobalProductRequest;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class GlobalProductRequestExporter extends Exporter
{
    protected static ?string $model = GlobalProductRequest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('tenant.slug'),
            ExportColumn::make('requestedBy.email'),
            ExportColumn::make('status'),
            ExportColumn::make('suggested_name'),
            ExportColumn::make('suggested_description'),
            ExportColumn::make('suggestedCategory.slug'),
            ExportColumn::make('admin_note'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your global product request export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
