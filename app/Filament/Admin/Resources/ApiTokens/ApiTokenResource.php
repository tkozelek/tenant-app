<?php

namespace App\Filament\Admin\Resources\ApiTokens;

use App\Filament\Admin\Resources\ApiTokens\Pages\CreateApiToken;
use App\Filament\Admin\Resources\ApiTokens\Pages\ListApiTokens;
use App\Filament\Admin\Resources\ApiTokens\Schemas\ApiTokenForm;
use App\Filament\Admin\Resources\ApiTokens\Tables\ApiTokensTable;
use App\Models\ApiToken;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ApiTokenResource extends Resource
{
    protected static ?string $model = ApiToken::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Key;

    protected static string|UnitEnum|null $navigationGroup = 'Použivatelia';

    protected static ?int $navigationSort = 100;

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
