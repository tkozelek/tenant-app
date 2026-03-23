<?php

namespace App\Filament\Tenant\Resources\TenantProductVariants\Schemas;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\components\VariantAttributesSection;
use App\Filament\Admin\Resources\TenantProductVariants\Schemas\actions\QuantityPriceRepeater;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantProductVariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail a zásoby')
                    ->schema([
                        Select::make('tenant_product_id')
                            ->label('Produkt')
                            ->relationship(
                                'product',
                                'name',
                                fn ($query) => $query->where('tenant_id', Filament::getTenant()?->id)
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->columnSpan(2),

                        TextInput::make('name')
                            ->label('Názov variantu')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        TextInput::make('sku')
                            ->label('SKU (Skladové číslo)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        TextInput::make('ean')
                            ->label('EAN kód')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        TextInput::make('stock_quantity')
                            ->label('Skladová zásoba')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->disabledOn('edit')
                            ->dehydrated()
                            ->helperText(fn (string $operation): string => $operation === 'edit'
                                ? 'Pre úpravu skladu stlačte tlačidlo úprava skladu.'
                                : '')
                            ->columnSpan(2),
                    ])->columns(2),

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

                        QuantityPriceRepeater::make(),
                    ]),

                VariantAttributesSection::make(),

                Section::make('Obrázky')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->collection('tenant_product_variants')
                            ->multiple()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->label('Obrázky variantu')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
