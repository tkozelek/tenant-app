<?php

namespace App\Filament\Admin\Resources\TenantProducts\Pages;

use App\Filament\Admin\Resources\TenantProducts\TenantProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantProduct extends CreateRecord
{
    protected static string $resource = TenantProductResource::class;
}
