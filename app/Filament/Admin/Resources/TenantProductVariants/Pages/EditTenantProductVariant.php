<?php

namespace App\Filament\Admin\Resources\TenantProductVariants\Pages;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\AdjustPriceAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\AdjustStockAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\FlashSaleAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\HistoryAction;
use App\Filament\Admin\Resources\TenantProducts\TenantProductResource;
use App\Filament\Admin\Resources\TenantProductVariants\TenantProductVariantResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditTenantProductVariant extends EditRecord
{
    protected static string $resource = TenantProductVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->icon('heroicon-o-trash'),
            Action::make('parent')
                ->label('Rodič')
                ->color('info')
                ->icon('heroicon-o-arrow-uturn-left')
                ->url(fn ($record): string => TenantProductResource::getUrl('edit', ['record' => $record->tenant_product_id])),
            HistoryAction::make(),
            AdjustPriceAction::make(),
            FlashSaleAction::make(),
            AdjustStockAction::make()
                ->after(fn () => $this->refreshFormData(['stock_quantity'])),
        ];
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Upravit '.$this->record->name;
    }
}
