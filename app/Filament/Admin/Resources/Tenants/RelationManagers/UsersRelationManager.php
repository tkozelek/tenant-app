<?php

namespace App\Filament\Admin\Resources\Tenants\RelationManagers;

use App\Filament\Admin\Resources\Tenants\RelationManagers\actions\AttachTenantUserAction;
use App\Filament\Admin\Resources\Tenants\RelationManagers\Schemas\TenantUserForm;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\PermissionRegistrar;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'email';

    public function form(Schema $schema): Schema
    {
        return TenantUserForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->columns([
                TextColumn::make('last_name')
                    ->label('Priezvisko')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('tenant_role')
                    ->label('Rola')
                    ->getStateUsing(fn (Model $record) => Role::find($record->pivot?->role_id)?->name ?? 'Bez roly')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('roles', 'name', fn ($query) => $query
                        ->whereDoesntHave('permissions', fn ($q) => $q->where('name', 'platform.access'))
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => array_merge($data, ['model_type' => User::class]))
                    ->after(fn () => app(PermissionRegistrar::class)->forgetCachedPermissions()),
                AttachTenantUserAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
