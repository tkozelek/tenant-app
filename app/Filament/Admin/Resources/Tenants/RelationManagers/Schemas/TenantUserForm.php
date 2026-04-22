<?php

namespace App\Filament\Admin\Resources\Tenants\RelationManagers\Schemas;

use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TenantUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('Meno')
                    ->required()
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->label('Priezvisko')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),

                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->rule(Password::default()),

                Select::make('role_id')
                    ->label('Rola')
                    ->options(
                        Role::whereDoesntHave('permissions', fn ($q) => $q->where('name', 'platform.access'))
                            ->pluck('name', 'id')
                    )
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }
}
