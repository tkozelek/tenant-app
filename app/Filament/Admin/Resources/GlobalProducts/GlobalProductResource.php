<?php

namespace App\Filament\Admin\Resources\GlobalProducts;

use App\Filament\Admin\Resources\GlobalProducts\Pages\CreateGlobalProduct;
use App\Filament\Admin\Resources\GlobalProducts\Pages\EditGlobalProduct;
use App\Filament\Admin\Resources\GlobalProducts\Pages\ListGlobalProducts;
use App\Filament\Admin\Resources\GlobalProducts\RelationManagers\TenantProductsRelationManager;
use App\Filament\Admin\Resources\GlobalProducts\Schemas\GlobalProductForm;
use App\Filament\Admin\Resources\GlobalProducts\Tables\GlobalProductsTable;
use App\Models\GlobalProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GlobalProductResource extends Resource
{
    protected static ?string $model = GlobalProduct::class;

    protected static ?string $modelLabel = 'Glob. produkt';

    protected static ?string $pluralModelLabel = 'Glob. produkty';

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
