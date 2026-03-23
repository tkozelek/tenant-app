<?php

namespace App\Filament\Tenant\Resources\Bundles\Pages;

use App\Filament\Tenant\Resources\Bundles\BundleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBundle extends CreateRecord
{
    protected static string $resource = BundleResource::class;
}
