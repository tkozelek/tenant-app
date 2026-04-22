<?php

namespace App\Filament\Exports;

use App\Models\Coupon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class CouponExporter extends Exporter
{
    protected static ?string $model = Coupon::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code')
                ->label('Kod'),
            ExportColumn::make('discount_type')
                ->label('Typ zlavy'),
            ExportColumn::make('value')
                ->label('Hodnota'),
            ExportColumn::make('min_order_amount')
                ->label('Min hodnota'),
            ExportColumn::make('usage_limit')
                ->label('Limit pouzitii'),
            ExportColumn::make('used_count')
                ->label('Pocet pouzitii'),
            ExportColumn::make('starts_at')
                ->label('Platny od'),
            ExportColumn::make('expires_at')
                ->label('Platny do'),
            ExportColumn::make('is_active')
                ->label('Aktivny'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export kuponov dokonceny. '.Number::format($export->successful_rows).' '.str('zaznam')->plural($export->successful_rows).' exportovanych.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('zaznam')->plural($failedRowsCount).' zlyhalo.';
        }

        return $body;
    }
}
