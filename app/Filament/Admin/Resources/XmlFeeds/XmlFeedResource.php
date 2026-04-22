<?php

namespace App\Filament\Admin\Resources\XmlFeeds;

use App\Filament\Admin\Resources\XmlFeeds\Pages\CreateXmlFeed;
use App\Filament\Admin\Resources\XmlFeeds\Pages\EditXmlFeed;
use App\Filament\Admin\Resources\XmlFeeds\Pages\ListXmlFeeds;
use App\Filament\Admin\Resources\XmlFeeds\Schemas\XmlFeedForm;
use App\Filament\Admin\Resources\XmlFeeds\Tables\XmlFeedsTable;
use App\Models\XmlFeed;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class XmlFeedResource extends Resource
{
    protected static ?string $model = XmlFeed::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRss;

    protected static ?int $navigationSort = 50;

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
