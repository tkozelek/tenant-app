<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Role name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        // TextInput::make('guard_name')
                        //     ->default('web')
                        //     ->required(),
                    ])->columns(1),

                Section::make('Permissions')
                    ->description('Select permissions for this role.')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->relationship(name: 'permissions', titleAttribute: 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->gridDirection('row'),
                    ]),
            ]);
    }
}
