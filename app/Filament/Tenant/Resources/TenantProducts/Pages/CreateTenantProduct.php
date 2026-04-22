<?php

namespace App\Filament\Tenant\Resources\TenantProducts\Pages;

use App\Filament\Tenant\Resources\TenantProducts\TenantProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantProduct extends CreateRecord
{
    protected static string $resource = TenantProductResource::class;
}
