<?php

namespace App\Filament\Admin\Resources\TenantProductVariants\Schemas;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\components\VariantAttributesSection;
use App\Filament\Admin\Resources\TenantProductVariants\Schemas\actions\QuantityPriceRepeater;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
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
                            ->required(fn ($livewire) => ! ($livewire instanceof RelationManager)) // ci nie je edit modal !! important
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
