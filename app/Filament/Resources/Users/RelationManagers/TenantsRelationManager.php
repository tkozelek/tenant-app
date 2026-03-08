<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\Role;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TenantsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('tenant_role')
                    ->label('Role')
                    ->getStateUsing(function (Model $record) {
                        $roleId = $record->pivot?->role_id;

                        return $roleId ? Role::find($roleId)?->name : 'No Role';
                    })
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role_id')
                            ->label('Role')
                            ->options(Role::whereDoesntHave('permissions', fn ($q) => $q->where('name', 'platform.access'))
                                ->pluck('name', 'id')
                            )
                            ->required(),
                    ])
                    ->mutateDataUsing(function (array $data): array {
                        $data['model_type'] = User::class;

                        return $data;
                    })
                    ->after(function () {
                        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema([
                        Select::make('role_id')
                            ->label('Role')
                            ->options(Role::whereDoesntHave('permissions', fn ($q) => $q->where('name', 'platform.access'))->pluck('name', 'id'))
                            ->required()
                            ->default(fn (Model $record) => $record->pivot?->role_id),
                    ])
                    ->mutateRecordDataUsing(function (array $data, Model $record): array {
                        $this->getOwnerRecord()->tenants()->updateExistingPivot($record->id, [
                            'role_id' => $data['role_id'],
                        ]);

                        return $data;
                    })
                    ->after(function () {
                        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                    }),
                DetachAction::make()
                    ->after(function () {
                        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
