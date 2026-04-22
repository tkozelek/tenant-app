<?php

namespace App\Filament\Tenant\Resources\GlobalProductRequests\Pages;

use App\Filament\Tenant\Resources\GlobalProductRequests\GlobalProductRequestResource;
use App\Models\GlobalProductRequest;
use Filament\Resources\Pages\EditRecord;

class EditGlobalProductRequest extends EditRecord
{
    protected static string $resource = GlobalProductRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var GlobalProductRequest $record */
        $record = $this->getRecord();

        if ($record->status !== 'pending') {
            $this->halt();
        }

        return $data;
    }
}
