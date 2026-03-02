<?php

namespace App\Filament\Resources\GlobalProductRequests\Pages;

use App\Filament\Resources\GlobalProductRequests\GlobalProductRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGlobalProductRequest extends EditRecord
{
    protected static string $resource = GlobalProductRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
