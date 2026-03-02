<?php

namespace App\Filament\Resources\TenantProducts\Pages;

use App\Filament\Resources\TenantProducts\TenantProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantProduct extends CreateRecord
{
    protected static string $resource = TenantProductResource::class;
}
