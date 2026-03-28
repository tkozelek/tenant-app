<?php

namespace App\Filament\Admin\Resources\Tenants\Pages;

use App\Filament\Admin\Resources\Tenants\TenantResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewTenant')
                ->label('Zobrazit')
                ->url(fn () => route('tenant.show', $this->record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
