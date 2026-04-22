<?php

namespace App\Filament\Admin\Resources\GlobalProductRequests\Pages;

use App\Filament\Admin\Resources\GlobalProductRequests\GlobalProductRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlobalProductRequest extends CreateRecord
{
    protected static string $resource = GlobalProductRequestResource::class;
}
