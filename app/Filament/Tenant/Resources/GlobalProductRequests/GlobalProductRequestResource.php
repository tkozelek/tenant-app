<?php

namespace App\Filament\Tenant\Resources\GlobalProductRequests;

use App\Filament\Tenant\Resources\GlobalProductRequests\Pages\CreateGlobalProductRequest;
use App\Filament\Tenant\Resources\GlobalProductRequests\Pages\EditGlobalProductRequest;
use App\Filament\Tenant\Resources\GlobalProductRequests\Pages\ListGlobalProductRequests;
use App\Filament\Tenant\Resources\GlobalProductRequests\Schemas\GlobalProductRequestForm;
use App\Filament\Tenant\Resources\GlobalProductRequests\Tables\GlobalProductRequestsTable;
use App\Models\GlobalProductRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GlobalProductRequestResource extends Resource
{
    protected static ?string $model = GlobalProductRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    public static function getNavigationBadge(): ?string
    {
        return (string) GlobalProductRequest::where('status', 'pending')
            ->where('tenant_id', filament()->getTenant()?->id)
            ->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return GlobalProductRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlobalProductRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGlobalProductRequests::route('/'),
            'create' => CreateGlobalProductRequest::route('/create'),
            'edit' => EditGlobalProductRequest::route('/{record}/edit'),
        ];
    }
}
