<?php

namespace App\Filament\Admin\Resources\Attributes\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Informacie')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true) // lost focus
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),

                            // vyber aky je dany atribut
                            Select::make('type')
                                ->options([
                                    'select' => 'Select',
                                    'number' => 'Number',
                                    'bool' => 'Boolean',
                                    'text' => 'Text',
                                ])
                                ->required()
                                ->live(),
                            // jednotky
                            TextInput::make('unit')
                                ->maxLength(50)
                                ->placeholder('e.g., GB, kg, cm')
                                ->helperText('Nechaj prázdne v prípad potreby'),
                            // ci sa da pomocou neho filtrovat
                            Toggle::make('is_filterable')
                                ->default(true)
                                ->inline(false),
                        ]),

                    Section::make('Kategorie')
                        ->schema([
                            Select::make('categories')
                                ->relationship('categories', 'name') // automaticky filament prepoj s tabulkou categorie.. aj pre ukladania fetchovanie a podobne
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->helperText('Vyber kategorie ktorym patri atribút'),
                        ]),
                ]),
                Group::make([
                    // viditelne iba ak atribut je typu select.. vyberame medzi x hodnotami
                    // repeater pridava viac moznosti.. ako napriklad farby... cervena, modra atp..
                    // 1 riadok - 1 attributeValue
                    Section::make('Atribút hodnoty')
                        ->schema([
                            Repeater::make('attributeValues')
                                ->relationship() // attributeValues relationship v modely
                                ->schema([
                                    TextInput::make('value')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                    TextInput::make('slug')
                                        ->required()
                                        ->unique(table: 'attribute_values', column: 'slug', ignoreRecord: true),
                                ])
                                ->columns(2)
                                ->orderColumn('sort_order')
                                ->defaultItems(1)
                                ->reorderableWithButtons(),
                        ])
                        ->visible(fn (Get $get) => $get('type') === 'select'),
                ]),
            ]);
    }
}
