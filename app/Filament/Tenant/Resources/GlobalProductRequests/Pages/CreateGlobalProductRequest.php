<?php

namespace App\Filament\Tenant\Resources\GlobalProductRequests\Pages;

use App\Filament\Tenant\Resources\GlobalProductRequests\GlobalProductRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlobalProductRequest extends CreateRecord
{
    protected static string $resource = GlobalProductRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requested_by_user_id'] = auth()->id();
        $data['status'] = 'pending';

        return $data;
    }
}
