<?php

namespace App\Filament\Tenant\Resources\ApiTokens;

use App\Filament\Tenant\Resources\ApiTokens\Pages\CreateApiToken;
use App\Filament\Tenant\Resources\ApiTokens\Pages\ListApiTokens;
use App\Filament\Tenant\Resources\ApiTokens\Schemas\ApiTokenForm;
use App\Filament\Tenant\Resources\ApiTokens\Tables\ApiTokensTable;
use App\Models\ApiToken;
use App\Models\Tenant;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ApiTokenResource extends Resource
{
    protected static ?string $model = ApiToken::class;

    protected static ?string $modelLabel = 'API token';

    protected static ?string $pluralModelLabel = 'API tokeny';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Key;

    protected static string|UnitEnum|null $navigationGroup = 'Použivatelia';

    protected static ?int $navigationSort = 100;

    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tokenable_type', Tenant::class)
            ->where('tokenable_id', Filament::getTenant()->id);
    }

    public static function form(Schema $schema): Schema
    {
        return ApiTokenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApiTokensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiTokens::route('/'),
            'create' => CreateApiToken::route('/create'),
        ];
    }
}
