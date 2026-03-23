<?php

namespace App\Filament\Tenant\Resources\TenantProductVariants\Pages;

use App\Filament\Tenant\Resources\TenantProductVariants\TenantProductVariantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantProductVariant extends CreateRecord
{
    protected static string $resource = TenantProductVariantResource::class;
}
