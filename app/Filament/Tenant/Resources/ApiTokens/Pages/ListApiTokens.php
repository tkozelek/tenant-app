<?php

namespace App\Filament\Tenant\Resources\ApiTokens\Pages;

use App\Filament\Tenant\Resources\ApiTokens\ApiTokenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
