<?php

namespace App\Filament\Resources\TenantProducts\Schemas;

use App\Models\GlobalProduct;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Prepojenie')
                    ->schema([
                        Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('global_product_id')
                            ->relationship('globalProduct', 'name')
                            ->placeholder('-')
                            ->searchable()
                            ->preload()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, $state) {
                                if ($state) {
                                    $globalProduct = GlobalProduct::find($state);
                                    if ($globalProduct) {
                                        $set('name', $globalProduct->name);
                                        $set('description', $globalProduct->description);
                                    }
                                }
                            }),
                    ])->columns(),
                Section::make('Detail')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Aktivny')
                            ->default(true),
                    ])
            ]);
    }
}
