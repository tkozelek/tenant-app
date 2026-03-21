<?php

namespace App\Filament\Admin\Resources\TenantProductVariants;

use App\Filament\Admin\Resources\TenantProductVariants\Pages\CreateTenantProductVariant;
use App\Filament\Admin\Resources\TenantProductVariants\Pages\EditTenantProductVariant;
use App\Filament\Admin\Resources\TenantProductVariants\Pages\ListTenantProductVariants;
use App\Filament\Admin\Resources\TenantProductVariants\Schemas\TenantProductVariantForm;
use App\Filament\Admin\Resources\TenantProductVariants\Tables\TenantProductVariantsTable;
use App\Models\TenantProductVariant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class TenantProductVariantResource extends Resource
{
    protected static ?string $model = TenantProductVariant::class;

    protected static string | UnitEnum | null $navigationGroup = "Tenant produkty";

    protected static ?int $navigationSort = 35;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TenantProductVariantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        $table = TenantProductVariantsTable::configure($table, true);

        return $table->modifyQueryUsing(fn(Builder $query) => $query->with('product.tenant'));
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
