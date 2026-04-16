<?php

namespace App\Filament\Tenant\Resources\ApiTokens\Pages;

use App\Filament\Tenant\Resources\ApiTokens\ApiTokenResource;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateApiToken extends CreateRecord
{
    protected static string $resource = ApiTokenResource::class;

    public ?string $plainTextToken = null;

    protected function handleRecordCreation(array $data): Model
    {
        $tenant = Filament::getTenant();

        $abilities = $data['abilities'] ?? ['*'];
        $expiresAt = isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null;

        /** @var \Laravel\Sanctum\NewAccessToken $newToken */
        $newToken = $tenant->createToken($data['name'], $abilities, $expiresAt);

        $newToken->accessToken->forceFill([
            'user_id' => auth()->id(),
        ])->save();

        $this->plainTextToken = $newToken->plainTextToken;

        return $newToken->accessToken;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Token bol vytvorený')
            ->body('Skopírujte si token teraz - nebude znovu zobrazený: '.$this->plainTextToken)
            ->success()
            ->persistent()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
