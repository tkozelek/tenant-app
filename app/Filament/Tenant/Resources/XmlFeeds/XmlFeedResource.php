<?php

namespace App\Filament\Tenant\Resources\XmlFeeds;

use App\Filament\Tenant\Resources\XmlFeeds\Pages\CreateXmlFeed;
use App\Filament\Tenant\Resources\XmlFeeds\Pages\EditXmlFeed;
use App\Filament\Tenant\Resources\XmlFeeds\Pages\ListXmlFeeds;
use App\Filament\Tenant\Resources\XmlFeeds\Schemas\XmlFeedForm;
use App\Filament\Tenant\Resources\XmlFeeds\Tables\XmlFeedsTable;
use App\Models\XmlFeed;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class XmlFeedResource extends Resource
{
    protected static ?string $model = XmlFeed::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRss;

    protected static string|UnitEnum|null $navigationGroup = 'Integracie';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return XmlFeedForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return XmlFeedsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListXmlFeeds::route('/'),
            'create' => CreateXmlFeed::route('/create'),
            'edit' => EditXmlFeed::route('/{record}/edit'),
        ];
    }
}
