<?php

namespace App\Filament\Admin\Resources\GlobalProductRequests\Pages;

use App\Filament\Admin\Resources\GlobalProductRequests\GlobalProductRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGlobalProductRequests extends ListRecords
{
    protected static string $resource = GlobalProductRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
