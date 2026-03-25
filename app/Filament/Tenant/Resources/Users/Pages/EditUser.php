<?php

namespace App\Filament\Tenant\Resources\Users\Pages;

use App\Filament\Tenant\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    private ?int $roleId = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $tenantId = Filament::getTenant()->id;

        $data['role_id'] = $this->record->roles()
            ->wherePivot('tenant_id', $tenantId)
            ->first()
            ?->id;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->roleId = $data['role_id'] ?? null;
        unset($data['role_id']);

        return $data;
    }

    protected function afterSave(): void
    {
        $tenantId = Filament::getTenant()->id;

        setPermissionsTeamId($tenantId);

        $this->record->roles()->wherePivot('tenant_id', $tenantId)->detach();

        if ($this->roleId) {
            $this->record->assignRole($this->roleId);
        }

        setPermissionsTeamId(null);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Odstrániť z prevádzky')
                ->action(function (): void {
                    $tenantId = Filament::getTenant()->id;

                    $this->record->roles()
                        ->wherePivot('tenant_id', $tenantId)
                        ->detach();

                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->requiresConfirmation(),
        ];
    }
}
