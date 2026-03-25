<?php

namespace App\Filament\Tenant\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ApiDocumentation extends Page
{
    protected static ?string $title = 'API Dokumentácia';

    protected static ?string $navigationLabel = 'Dokumentácia';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'API';

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        return $user?->hasPermissionTo('tenant.view_api_docs') ?? false;
    }

    public function getView(): string
    {
        return 'filament.tenant.pages.api-documentation';
    }
}
