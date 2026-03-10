<?php

namespace App\Filament\Resources\Bundles\Schemas;

use App\Filament\Actions\GenerateDescipritonAction;
use App\Filament\Resources\TenantProductVariants\Schemas\actions\QuantityPriceRepeater;
use App\Models\TenantProductVariant;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BundleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                ->schema([
                    Section::make('Detail')
                        ->schema([
                            Select::make('tenant_id')
                                ->relationship('tenant', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->label('Tenant')
                                ->live()
                                ->columnSpanFull(),
                            TextInput::make('name')
                                ->label('Názov')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->unique(ignoreRecord: true),
                            RichEditor::make('description')
                                ->hintAction(
                                    GenerateDescipritonAction::make()
                                        ->context("balik (bundle viacerych produktov)")
                                        ->title('name')
                                        ->references('description')
                                )
                                ->columnSpanFull()
                                ->label('Popis'),
                            Toggle::make('is_active')
                                ->default(true)
                                ->required()
                        ])->columns(),
                ]),
                Group::make()
                ->schema([
                    Section::make('Cenotvorba')
                        ->schema([
                            TextInput::make('price')
                                ->label('Aktuálna cena')
                                ->required()
                                ->numeric()
                                ->prefix('€')
                                ->step('0.01'),

                            TextInput::make('original_price')
                                ->label('Pôvodná cena (pred zľavou)')
                                ->numeric()
                                ->prefix('€')
                                ->step('0.01')
                                ->helperText('Vyplňte, ak je produkt v zľave.'),
                        ]),
                    Section::make('Produkty')
                        ->schema([
                            Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    Select::make('tenant_product_variant_id')
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->placeholder('Vyberte produkt variantu')
                                        ->options(function (Get $get) {
                                            $tenantId = $get('../../tenant_id');

                                            $query = TenantProductVariant::query();

                                            if ($tenantId) {
                                                $query->whereHas('product', function ($q) use ($tenantId) {
                                                    $q->where('tenant_id', $tenantId);
                                                });
                                            }

                                            return $query->pluck('name', 'id');
                                        })
                                        ->label('Produkt variant'),

                                    TextInput::make('quantity')
                                        ->label('Pocet')
                                        ->required()
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1),
                                ])
                                ->columns()
                                ->defaultItems(1)
                                ->addActionLabel('Pridať variant do balika'),
                        ])
                    ])
            ]);
    }
}
