<?php

namespace App\Filament\Admin\Resources\ApiTokens\Schemas;

use App\Enums\ApiPermission;
use App\Models\Tenant;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApiTokenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informacie')
                    ->schema([
                        Select::make('tenant_id')
                            ->label('Tenant')
                            ->options(Tenant::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        TextInput::make('name')
                            ->label('Názov tokenu')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('napr. Eshop integrácia'),

                        DateTimePicker::make('expires_at')
                            ->label('Platí do')
                            ->nullable()
                            ->helperText('Prázdne pre neobmedzenú platnost')
                            ->columnSpan(2),
                    ])->columns(2),

                Section::make('Oprávnenia')
                    ->description('Vyberte k čomu má token prístup')
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
