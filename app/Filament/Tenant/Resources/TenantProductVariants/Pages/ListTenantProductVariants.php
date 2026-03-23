<?php

namespace App\Filament\Tenant\Resources\TenantProductVariants\Pages;

use App\Filament\Tenant\Resources\TenantProductVariants\TenantProductVariantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantProductVariants extends ListRecords
{
    protected static string $resource = TenantProductVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
