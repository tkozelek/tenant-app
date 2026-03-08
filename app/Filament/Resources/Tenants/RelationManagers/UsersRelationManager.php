<?php

namespace App\Filament\Resources\Tenants\RelationManagers;

use App\Models\Role;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder; // Opravený import
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'email';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true) // Opravené: ignoruje aktuálny záznam pri EditAction
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->rule(Password::default()),
                Select::make('role_id')
                    ->label('Role')
                    ->options(Role::whereDoesntHave('permissions', fn ($q) => $q->where('name', 'platform.access'))
                        ->pluck('name', 'id')
                    )
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->columns([
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('tenant_role')
                    ->label('Role')
                    ->getStateUsing(function (Model $record) {
                        $roleId = $record->pivot?->role_id;

                        return $roleId ? \App\Models\Role::find($roleId)?->name : 'No Role';
                    })
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
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['model_type'] = User::class;

                        return $data;
                    })
                    ->after(function () {
                        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                    }),
                AttachAction::make()
                    ->preloadRecordSelect(false)
                    ->recordSelectSearchColumns(['email', 'last_name', 'first_name'])
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        return $query->withoutGlobalScopes();
                    })
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
