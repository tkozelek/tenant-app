<?php

namespace App\Filament\Tenant\Resources\TenantProductVariants\Schemas;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\components\VariantAttributesSection;
use App\Filament\Admin\Resources\TenantProductVariants\Schemas\actions\QuantityPriceRepeater;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
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

                Section::make('Cenotvorba')
                    ->schema([
                        TextEntry::make('current_price_display')
                            ->label('Aktualna cena')
                            ->state(fn (?TenantProductVariant $record): string => $record?->current_price_formatted ?? '-')
                            ->hiddenOn('create'),

                        TextEntry::make('current_original_price_display')
                            ->label('Povodna cena')
                            ->state(fn (?TenantProductVariant $record): string => $record?->current_original_price_formatted ?? '-')
                            ->hiddenOn('create'),

                        TextInput::make('initial_price')
                            ->label('Cena')
                            ->numeric()
                            ->prefix('€')
                            ->step('0.01')
                            ->visibleOn('create'),

                        TextInput::make('initial_original_price')
                            ->label('Povodna cena (pred zlavou)')
                            ->numeric()
                            ->prefix('€')
                            ->step('0.01')
                            ->helperText('Vyplnte, ak je produkt v zlave.')
                            ->visibleOn('create'),

                        DateTimePicker::make('initial_valid_from')
                            ->label('Platne od')
                            ->default(now())
                            ->helperText('Pre okamzitu zmenu nechajte aktualny cas.')
                            ->visibleOn('create'),

                        DateTimePicker::make('initial_valid_to')
                            ->label('Platne do')
                            ->after('initial_valid_from')
                            ->helperText('Nepovinne. Ak je vyplnene, cena sa automaticky deaktivuje po tomto datume.')
                            ->visibleOn('create'),

                        QuantityPriceRepeater::make(),
                    ]),

                VariantAttributesSection::make(),

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
            ]);
    }
}
