<?php

namespace App\Filament\Admin\Resources\Users\RelationManagers\actions;

use App\Models\Role;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Forms\Components\Select;
use Spatie\Permission\PermissionRegistrar;

class AttachTenantAction
{
    public static function make(): AttachAction
    {
        return AttachAction::make()
            ->preloadRecordSelect()
            ->schema(fn (AttachAction $action): array => [
                $action->getRecordSelect()->multiple(),
                Select::make('role_id')
                    ->label('Rola')
                    ->options(
                        Role::whereDoesntHave('permissions', fn ($q) => $q->where('name', 'platform.access'))
                            ->pluck('name', 'id')
                    )
                    ->required(),
            ])
            ->mutateDataUsing(function (array $data): array {
                $data['model_type'] = User::class;

                return $data;
            })
            ->after(fn () => app(PermissionRegistrar::class)->forgetCachedPermissions());
    }
}
