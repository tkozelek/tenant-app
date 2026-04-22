<?php

namespace App\Filament\Admin\Resources\TenantProducts\Pages;

use App\Filament\Admin\Resources\TenantProducts\TenantProductResource;
use App\Filament\Admin\Widgets\TenantProductPriceHistoryChart;
use App\Models\TenantProduct;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Support\Enums\Width;

class EditTenantProduct extends EditRecord
{
    protected static string $resource = TenantProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('price_history')
                ->label('Historia ceny')
                ->color('info')
                ->modalHeading(fn () => "Historia ceny: {$this->record->name}")
                ->modalSubmitAction(false)
                ->modalWidth(Width::SevenExtraLarge)
                ->modalCancelActionLabel('Zavriet')
                ->schema([
                    Livewire::make(TenantProductPriceHistoryChart::class, fn (TenantProduct $record) => [
                        'record' => $record,
                    ])->columnSpanFull(),
                ]),
            DeleteAction::make(),
        ];
    }
}
