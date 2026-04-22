<?php

namespace App\Filament\Tenant\Resources\ApiTokens\Schemas;

use App\Enums\ApiPermission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApiTokenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Základné informácie')
                    ->schema([
                        TextInput::make('name')
                            ->label('Názov tokenu')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('napr. Eshop integrácia'),

                        DateTimePicker::make('expires_at')
                            ->label('Platnosť do')
                            ->nullable()
                            ->helperText('Nechajte prázdne pre neobmedzenú platnosť.'),
                    ])->columns(2),

                Section::make('Oprávnenia')
                    ->description('Vyberte, ku ktorým zdrojom má token prístup.')
                    ->schema([
                        CheckboxList::make('abilities')
                            ->label(false)
                            ->options(ApiPermission::options())
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable()
                            ->required(),
                    ]),
            ]);
    }
}
