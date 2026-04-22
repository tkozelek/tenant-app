<?php

namespace App\Filament\Admin\Resources\Users\RelationManagers\actions;

use App\Enums\PermissionScope;
use App\Models\Role;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\PermissionRegistrar;

class EditTenantRoleAction
{
    public static function make(): EditAction
    {
        return EditAction::make()
            ->schema([
                Select::make('role_id')
                    ->label('Rola')
                    ->options(
                        Role::where('scope', PermissionScope::Tenant)->pluck('name', 'id')
                    )
                    ->required()
                    ->default(fn (Model $record) => $record->pivot?->role_id),
            ])
            ->mutateRecordDataUsing(function (array $data, Model $record, $livewire): array {
                $livewire->getOwnerRecord()->tenants()->updateExistingPivot($record->id, [
                    'role_id' => $data['role_id'],
                ]);

                return $data;
            })
            ->after(fn () => app(PermissionRegistrar::class)->forgetCachedPermissions());
    }
}
