<?php

namespace App\Filament\Tenant\Resources\Users\Pages;

use App\Filament\Tenant\Resources\Users\UserResource;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Spatie\Permission\PermissionRegistrar;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('attachUser')
                ->label('Pridať používateľa')
                ->form([
                    Select::make('user_id')
                        ->label('Používateľ (e-mail)')
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search): array {
                            $existingIds = Filament::getTenant()->allUsers()->pluck('id');

                            return User::where('email', 'like', "%{$search}%")
                                ->whereNotIn('id', $existingIds)
                                ->limit(10)
                                ->pluck('email', 'id')
                                ->toArray();
                        })
                        ->required(),

                    Select::make('role_id')
                        ->label('Rola')
                        ->options(fn (): array => Role::query()
                            ->where(fn ($query) => $query
                                ->whereDoesntHave('permissions', fn ($q) => $q->where('name', 'platform.access'))
                                ->orWhere('tenant_id', Filament::getTenant()?->id)
                            )
                            ->pluck('name', 'id')
                            ->toArray()
                        )
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $tenant = Filament::getTenant();
                    $user = User::findOrFail($data['user_id']);

                    setPermissionsTeamId($tenant->id);
                    $user->assignRole($data['role_id']);
                    setPermissionsTeamId(null);

                    app(PermissionRegistrar::class)->forgetCachedPermissions();
                })
                ->authorize('create', User::class),
        ];
    }
}
