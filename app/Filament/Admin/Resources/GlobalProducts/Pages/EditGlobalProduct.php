<?php

namespace App\Filament\Admin\Resources\GlobalProducts\Pages;

use App\Filament\Admin\Resources\GlobalProducts\GlobalProductResource;
use App\Filament\Admin\Resources\GlobalProducts\Schemas\actions\HistoryAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGlobalProduct extends EditRecord
{
    protected static string $resource = GlobalProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            HistoryAction::make(),
            DeleteAction::make(),
        ];
    }
}
