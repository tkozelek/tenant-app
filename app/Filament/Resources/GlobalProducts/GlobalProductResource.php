<?php

namespace App\Filament\Resources\GlobalProducts;

use App\Filament\Resources\GlobalProducts\Pages\CreateGlobalProduct;
use App\Filament\Resources\GlobalProducts\Pages\EditGlobalProduct;
use App\Filament\Resources\GlobalProducts\Pages\ListGlobalProducts;
use App\Filament\Resources\GlobalProducts\RelationManagers\TenantProductsRelationManager;
use App\Filament\Resources\GlobalProducts\Schemas\GlobalProductForm;
use App\Filament\Resources\GlobalProducts\Tables\GlobalProductsTable;
use App\Models\GlobalProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GlobalProductResource extends Resource
{
    protected static ?string $model = GlobalProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GlobalProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlobalProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TenantProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGlobalProducts::route('/'),
            'create' => CreateGlobalProduct::route('/create'),
            'edit' => EditGlobalProduct::route('/{record}/edit'),
        ];
    }
}
