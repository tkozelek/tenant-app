<?php

namespace App\Filament\Admin\Resources\TenantProducts\Pages;

use App\Filament\Admin\Resources\TenantProducts\TenantProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantProducts extends ListRecords
{
    protected static string $resource = TenantProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
