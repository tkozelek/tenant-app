<?php

namespace App\Filament\Admin\Resources\XmlFeeds\Pages;

use App\Filament\Admin\Resources\XmlFeeds\XmlFeedResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditXmlFeed extends EditRecord
{
    protected static string $resource = XmlFeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
