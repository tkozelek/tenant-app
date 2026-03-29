<?php

namespace App\Filament\Tenant\Resources\TenantProductVariants\Schemas;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\components\VariantAttributesSection;
use App\Filament\Components\MarketPriceStatsSection;
use App\Filament\Components\PriceMakingSection;
use App\Models\TenantProduct;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantProductVariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Group::make([
                            Section::make('Detail a zásoby')
                                ->schema([
                                    Select::make('tenant_product_id')
                                        ->label('Produkt')
                                        ->relationship(
                                            'product',
                                            'name',
                                            fn ($query) => $query->where('tenant_id', Filament::getTenant()?->id)
                                        )
                                        ->afterStateUpdated(function ($set, $state) {
                                            if ($state) {
                                                $product = TenantProduct::find($state);
                                                if ($product) {
                                                    $set('name', $product->name);
                                                }
                                            }
                                        })
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->columnSpan(2),

                                    TextInput::make('name')
                                        ->label('Nazov')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(2),

                                    TextInput::make('sku')
                                        ->label('SKU')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true)
                                        ->columnSpan(1),

                                    TextInput::make('ean')
                                        ->label('EAN')
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true)
                                        ->columnSpan(1),

                                    TextInput::make('stock_quantity')
                                        ->label('Sklad')
                                        ->required()
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0)
                                        ->disabledOn('edit')
                                        ->dehydrated()
                                        ->helperText(fn (string $operation): string => $operation === 'edit'
                                            ? 'Pre úpravu stlačte tladiclo sklad,.'
                                            : '')
                                        ->columnSpan(2),
                                ])->columns(2),

                            Section::make('Obrazky')
                                ->schema([
                                    SpatieMediaLibraryFileUpload::make('media')
                                        ->collection('tenant_product_variants')
                                        ->multiple()
                                        ->reorderable()
                                        ->panelLayout('grid')
                                        ->label('Obrázky')
                                        ->columnSpanFull(),
                                ]),

                            VariantAttributesSection::make(),

                        ])->columnSpan(1),

                        Group::make([
                            MarketPriceStatsSection::make(),
                            PriceMakingSection::make(),
                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
