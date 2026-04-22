<?php

namespace App\Filament\Admin\Resources\Users\RelationManagers;

use App\Filament\Admin\Resources\Users\RelationManagers\actions\AttachTenantAction;
use App\Filament\Admin\Resources\Users\RelationManagers\actions\EditTenantRoleAction;
use App\Models\Role;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\PermissionRegistrar;

class TenantsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenants';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Obchod')
                    ->searchable(),
                TextColumn::make('tenant_role')
                    ->label('Rola')
                    ->getStateUsing(fn (Model $record) => Role::find($record->pivot?->role_id)?->name ?? 'Bez roly')
                    ->badge()
                    ->color('info'),
            ])
            ->headerActions([
                AttachTenantAction::make(),
            ])
            ->recordActions([
                EditTenantRoleAction::make(),
                DetachAction::make()
                    ->after(fn () => app(PermissionRegistrar::class)->forgetCachedPermissions()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
