<?php

namespace App\Filament\Admin\Resources\XmlFeeds\Pages;

use App\Filament\Admin\Resources\XmlFeeds\XmlFeedResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListXmlFeeds extends ListRecords
{
    protected static string $resource = XmlFeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
