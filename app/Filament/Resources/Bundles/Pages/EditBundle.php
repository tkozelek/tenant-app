<?php

namespace App\Filament\Resources\Bundles\Pages;

use App\Filament\Resources\Bundles\BundleResource;
use App\Filament\Resources\Bundles\Schemas\actions\HistoryAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBundle extends EditRecord
{
    protected static string $resource = BundleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            HistoryAction::make(),
        ];
    }
}
