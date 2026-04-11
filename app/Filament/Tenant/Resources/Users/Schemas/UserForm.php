<?php

namespace App\Filament\Tenant\Resources\Users\Schemas;

use App\Enums\PermissionScope;
use App\Models\Role;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informacie')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Meno')
                            ->required()
                            ->disabled()
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->label('Priezvisko')
                            ->required()
                            ->disabled()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->disabled()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Pristup')
                    ->schema([
                        Select::make('role_id')
                            ->label('Rola')
                            ->options(fn (): array => Role::query()
                                ->where('scope', PermissionScope::Tenant)
                                ->where(function ($query): void {
                                    $query->whereNull('tenant_id')
                                        ->orWhere('tenant_id', Filament::getTenant()?->id);
                                })
                                ->pluck('name', 'id')
                                ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),
            ]);
    }
}
