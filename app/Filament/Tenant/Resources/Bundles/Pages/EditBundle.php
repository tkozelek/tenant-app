<?php

namespace App\Filament\Tenant\Resources\Bundles\Pages;

use App\Filament\Tenant\Resources\Bundles\BundleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBundle extends EditRecord
{
    protected static string $resource = BundleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
