<?php

namespace App\Filament\Admin\Resources\GlobalProducts\Pages;

use App\Filament\Admin\Resources\GlobalProducts\GlobalProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlobalProduct extends CreateRecord
{
    protected static string $resource = GlobalProductResource::class;
}
