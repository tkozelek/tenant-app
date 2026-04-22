<?php

namespace App\Filament\Admin\Resources\TenantProductVariants\Schemas;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\components\VariantAttributesSection;
use App\Filament\Components\MarketPriceStatsSection;
use App\Filament\Components\PriceMakingSection;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            Section::make('Detail a zasoby')
                                ->schema([
                                    Select::make('tenant_id')
                                        ->label('Tenant')
                                        ->options(fn () => Tenant::orderBy('name')->pluck('name', 'id'))
                                        ->searchable()
                                        ->live()
                                        ->dehydrated(false)
                                        ->afterStateHydrated(fn (Select $component, ?TenantProductVariant $record) => $component->state($record?->product?->tenant_id))
                                        ->afterStateUpdated(fn (Set $set) => $set('tenant_product_id', null))
                                        ->visible(fn ($livewire) => ! ($livewire instanceof RelationManager))
                                        ->required(fn ($livewire) => ! ($livewire instanceof RelationManager))
                                        ->columnSpan(2),

                                    Select::make('tenant_product_id')
                                        ->label('Produkt')
                                        ->options(fn (Get $get) => $get('tenant_id')
                                            ? TenantProduct::where('tenant_id', $get('tenant_id'))->orderBy('name')->pluck('name', 'id')
                                            : []
                                        )
                                        ->getOptionLabelUsing(fn ($value) => TenantProduct::find($value)?->name)
                                        ->searchable()
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, ?int $state): void {
                                            if (! $state) {
                                                return;
                                            }

                                            $product = TenantProduct::find($state);

                                            if ($product) {
                                                $set('name', $product->name);
                                            }
                                        })
                                        ->visible(fn ($livewire) => ! ($livewire instanceof RelationManager))
                                        ->required(fn ($livewire) => ! ($livewire instanceof RelationManager))
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

                                    TextInput::make('url')
                                        ->label('URL produktu v obchode')
                                        ->url()
                                        ->maxLength(2048)
                                        ->placeholder('https://...')
                                        ->helperText('Odkaz na produkt vo e-shope tenanta.')
                                        ->columnSpan(2),

                                    TextInput::make('stock_quantity')
                                        ->label('Skladová zásoba')
                                        ->required()
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0)
                                        ->disabledOn('edit')
                                        ->dehydrated()
                                        ->helperText(fn (string $operation): string => $operation === 'edit'
                                            ? 'Pre upravu skladu stlacte tlacidlo uprava skladu.'
                                            : '')
                                        ->columnSpan(2),
                                ])->columns(2),

                            Section::make('Obrázky')
                                ->schema([
                                    SpatieMediaLibraryFileUpload::make('media')
                                        ->collection('tenant_product_variants')
                                        ->multiple()
                                        ->reorderable()
                                        ->panelLayout('grid')
                                        ->visibility('public')
                                        ->label('Obrázky variantu')
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
