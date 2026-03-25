<?php

namespace App\Filament\Tenant\Resources\TenantProducts\Actions;

use App\Filament\Tenant\Widgets\TenantProductAveragePriceChart;
use App\Models\TenantProduct;
use Filament\Actions\Action;
use Filament\Schemas\Components\Livewire;
use Filament\Support\Enums\Width;

class ProductPriceHistoryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'product_price_history';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('História cien')
            ->color('info')
            ->modalHeading(fn (TenantProduct $record) => "Priemerný vývoj ceny: {$record->name}")
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Zavrieť')
            ->modalWidth(Width::SevenExtraLarge)
            ->schema([
                Livewire::make(TenantProductAveragePriceChart::class, fn (TenantProduct $record) => [
                    'record' => $record,
                ])->columnSpanFull(),
            ]);
    }
}
