<?php

namespace App\Filament\Admin\Resources\Tenants\RelationManagers\actions;

use App\Models\Role;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\PermissionRegistrar;

class AttachTenantUserAction
{
    public static function make(): AttachAction
    {
        return AttachAction::make()
            ->preloadRecordSelect(false)
            ->recordSelectSearchColumns(['email', 'last_name', 'first_name'])
            ->recordSelectOptionsQuery(fn (Builder $query) => $query->withoutGlobalScopes())
            ->schema(fn (AttachAction $action): array => [
                $action->getRecordSelect(),
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
