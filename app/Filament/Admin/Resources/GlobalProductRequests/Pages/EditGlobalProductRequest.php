<?php

namespace App\Filament\Admin\Resources\GlobalProductRequests\Pages;

use App\Filament\Admin\Resources\GlobalProductRequests\GlobalProductRequestResource;
use App\Filament\Admin\Resources\GlobalProductRequests\Tables\actions\ApproveAndCreateAction;
use App\Filament\Admin\Resources\GlobalProductRequests\Tables\actions\RejectAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGlobalProductRequest extends EditRecord
{
    protected static string $resource = GlobalProductRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ApproveAndCreateAction::make(),
            RejectAction::make(),
            DeleteAction::make(),
        ];
    }
}
