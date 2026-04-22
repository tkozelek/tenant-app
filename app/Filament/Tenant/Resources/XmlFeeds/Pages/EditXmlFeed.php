<?php

namespace App\Filament\Tenant\Resources\XmlFeeds\Pages;

use App\Filament\Tenant\Resources\XmlFeeds\XmlFeedResource;
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
