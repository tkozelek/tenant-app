<?php

namespace App\Filament\Tenant\Resources\TenantProducts\Pages;

use App\Filament\Tenant\Resources\TenantProducts\TenantProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantProduct extends EditRecord
{
    protected static string $resource = TenantProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
