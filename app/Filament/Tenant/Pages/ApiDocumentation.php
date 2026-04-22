<?php

namespace App\Filament\Tenant\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ApiDocumentation extends Page
{
    protected static ?string $title = 'API Dokumentácia';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'API';

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionToOnFilamentTenant('tenant.view_api_docs');
    }

    public function getView(): string
    {
        return 'filament.tenant.pages.api-documentation';
    }
}
