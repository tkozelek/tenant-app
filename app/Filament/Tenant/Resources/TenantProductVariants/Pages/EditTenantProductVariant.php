<?php

namespace App\Filament\Tenant\Resources\TenantProductVariants\Pages;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\AdjustStockAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\HistoryAction;
use App\Filament\Tenant\Resources\TenantProducts\TenantProductResource;
use App\Filament\Tenant\Resources\TenantProductVariants\TenantProductVariantResource;
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
            DeleteAction::make()->icon('heroicon-o-trash'),
            Action::make('parent')
                ->label('Rodič')
                ->color('info')
                ->icon('heroicon-o-arrow-uturn-left')
                ->url(fn (): string => TenantProductResource::getUrl('edit', ['record' => $this->record->tenant_product_id])),
            HistoryAction::make(),
            AdjustStockAction::make()
                ->after(fn () => $this->refreshFormData(['stock_quantity'])),
        ];
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Upraviť '.$this->record->name;
    }
}
