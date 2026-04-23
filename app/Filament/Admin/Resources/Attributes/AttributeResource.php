<?php

namespace App\Filament\Admin\Resources\Attributes;

use App\Filament\Admin\Resources\Attributes\Pages\CreateAttribute;
use App\Filament\Admin\Resources\Attributes\Pages\EditAttribute;
use App\Filament\Admin\Resources\Attributes\Pages\ListAttributes;
use App\Filament\Admin\Resources\Attributes\Schemas\AttributeForm;
use App\Filament\Admin\Resources\Attributes\Tables\AttributesTable;
use App\Models\Attribute;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $modelLabel = 'Atribút';

    protected static ?string $pluralModelLabel = 'Atribúty';

    protected static string|UnitEnum|null $navigationGroup = 'Kategorie';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 80;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AttributeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttributesTable::configure($table);
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
            'index' => ListAttributes::route('/'),
            'create' => CreateAttribute::route('/create'),
            'edit' => EditAttribute::route('/{record}/edit'),
        ];
    }
}
