<?php

namespace App\Filament\Tenant\Resources\ActivityLogs\Pages;

use App\Filament\Tenant\Resources\ActivityLogs\ActivityLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
