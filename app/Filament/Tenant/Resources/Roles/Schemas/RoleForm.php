<?php

namespace App\Filament\Tenant\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informácie')
                    ->schema([
                        TextInput::make('name')
                            ->label('Názov')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Popis')
                            ->nullable()
                            ->rows(2),
                    ])->columns(1),

                Section::make('Oprávnenia')
                    ->description('Vyberte oprávnenia pre túto rolu.')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->relationship(name: 'permissions', titleAttribute: 'name')
                            ->options(
                                Permission::query()
                                    ->where('name', 'like', 'tenant.%')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->gridDirection('row'),
                    ]),
            ]);
    }
}
