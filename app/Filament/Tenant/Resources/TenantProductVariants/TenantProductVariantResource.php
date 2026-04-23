<?php

namespace App\Filament\Tenant\Resources\TenantProductVariants;

use App\Filament\Tenant\Resources\TenantProductVariants\Pages\CreateTenantProductVariant;
use App\Filament\Tenant\Resources\TenantProductVariants\Pages\EditTenantProductVariant;
use App\Filament\Tenant\Resources\TenantProductVariants\Pages\ListTenantProductVariants;
use App\Filament\Tenant\Resources\TenantProductVariants\Schemas\TenantProductVariantForm;
use App\Filament\Tenant\Resources\TenantProductVariants\Tables\TenantProductVariantsTable;
use App\Models\TenantProductVariant;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class TenantProductVariantResource extends Resource
{
    protected static ?string $model = TenantProductVariant::class;

    protected static ?string $modelLabel = 'Variant';

    protected static ?string $pluralModelLabel = 'Varianty';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 50;

    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('product', fn (Builder $q) => $q->where('tenant_id', Filament::getTenant()?->id))
            ->with('product');
    }

    public static function form(Schema $schema): Schema
    {
        return TenantProductVariantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantProductVariantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantProductVariants::route('/'),
            'create' => CreateTenantProductVariant::route('/create'),
            'edit' => EditTenantProductVariant::route('/{record}/edit'),
        ];
    }
}
