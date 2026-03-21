<?php

namespace App\Filament\Admin\Resources\TenantProducts;

use App\Filament\Admin\Resources\TenantProducts\Pages\CreateTenantProduct;
use App\Filament\Admin\Resources\TenantProducts\Pages\EditTenantProduct;
use App\Filament\Admin\Resources\TenantProducts\Pages\ListTenantProducts;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\VariantsRelationManager;
use App\Filament\Admin\Resources\TenantProducts\Schemas\TenantProductForm;
use App\Filament\Admin\Resources\TenantProducts\Tables\TenantProductsTable;
use App\Models\TenantProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TenantProductResource extends Resource
{
    protected static ?string $model = TenantProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string | UnitEnum | null $navigationGroup = "Tenant produkty";

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TenantProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantProducts::route('/'),
            'create' => CreateTenantProduct::route('/create'),
            'edit' => EditTenantProduct::route('/{record}/edit'),
        ];
    }
}
