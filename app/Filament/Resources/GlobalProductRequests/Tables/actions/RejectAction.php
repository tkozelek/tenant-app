<?php

namespace App\Filament\Resources\GlobalProductRequests\Tables\actions;

use App\Models\GlobalProductRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class RejectAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reject';
    }

    public function setUp(): void {
            $this->label('Odmietnuť')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->visible(fn (GlobalProductRequest $record) => $record->status === 'pending')
            ->schema([
                Textarea::make('admin_note')
                    ->label('Poznámka (admin)')
                    ->required()
            ])
            ->action(function (array $data, GlobalProductRequest $record) {
                $record->update([
                    'status' => 'rejected',
                    'admin_note' => $data['admin_note']
                ]);

                Cache::forget('global_product_requests_count');
                Notification::make()->title('Request odmietnutý.')->danger()->send();
            });
    }
}
