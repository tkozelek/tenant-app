<?php

namespace App\Filament\Resources\GlobalProductRequests\Pages;

use App\Filament\Resources\GlobalProductRequests\GlobalProductRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlobalProductRequest extends CreateRecord
{
    protected static string $resource = GlobalProductRequestResource::class;
}
