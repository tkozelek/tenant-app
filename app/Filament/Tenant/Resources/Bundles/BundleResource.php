<?php

namespace App\Filament\Tenant\Resources\Bundles;

use App\Filament\Tenant\Resources\Bundles\Pages\CreateBundle;
use App\Filament\Tenant\Resources\Bundles\Pages\EditBundle;
use App\Filament\Tenant\Resources\Bundles\Pages\ListBundles;
use App\Filament\Tenant\Resources\Bundles\Schemas\BundleForm;
use App\Filament\Tenant\Resources\Bundles\Tables\BundlesTable;
use App\Models\Bundle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BundleResource extends Resource
{
    protected static ?string $model = Bundle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?int $navigationSort = 60;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|null|\UnitEnum $navigationGroup = 'Produkty';

    public static function form(Schema $schema): Schema
    {
        return BundleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BundlesTable::configure($table);
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
            'index' => ListBundles::route('/'),
            'create' => CreateBundle::route('/create'),
            'edit' => EditBundle::route('/{record}/edit'),
        ];
    }
}
