<?php

namespace App\Filament\Resources\TenantProducts\RelationManagers\components;

use App\Models\AttributeValue;
use App\Models\TenantProduct;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Livewire\Component;

class VariantAttributesSection
{
    public static function make()
    {
        return Section::make('Atribúty a parametre')
            ->description('Vyberte atributy a nastavte im hodnoty')
            ->schema([
                Repeater::make('variantAttributes')
                    ->relationship('variantAttributes')
                    ->label('')
                    ->defaultItems(0)
                    ->addActionLabel('Pridať')
                    ->schema([
                        Select::make('attribute_id')
                            ->label('Atribút')
                            ->options(fn (Component $livewire) => self::getCategoryAttributes($livewire)->pluck('name', 'id'))
                            ->required()
                            ->live()
                            // pri zmene vymazat
                            ->afterStateUpdated(function (Set $set) {
                                $set('attribute_value_id', null);
                                $set('custom_value', null);
                            })
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        // select
                        Select::make('attribute_value_id')
                            ->label('Hodnota')
                            ->options(fn (Get $get, Component $livewire) => self::value($get('attribute_id'), $livewire))
                            ->visible(fn (Get $get, Component $livewire) => self::type($get, $livewire) === 'select')
                            ->required(fn (Get $get, Component $livewire) => self::type($get, $livewire) === 'select'),

                        // custom
                        TextInput::make('custom_value')
                            ->label(fn (Get $get, Component $livewire) => 'Hodnota '.self::getAttribute($get, $livewire)?->unit)
                            ->visible(fn (Get $get, Component $livewire) => self::type($get, $livewire) !== 'select')
                            ->required(fn (Get $get, Component $livewire) => self::type($get, $livewire) !== 'select')
                            ->numeric(fn (Get $get, Component $livewire) => self::type($get, $livewire) === 'number')
                            ->helperText(fn (Get $get, Component $livewire) => self::type($get, $livewire) === 'bool' ? 'Zadajte 1/0' : null),
                    ])
                    ->columns()
                    ->columnSpanFull(),
            ]);
    }

    private static function getCategoryAttributes(Component $livewire)
    {
        static $attributes = null;

        if ($attributes !== null) {
            return $attributes;
        }
        // prve volanie, napln premenu
        if (method_exists($livewire, 'getOwnerRecord') && $livewire->getOwnerRecord()) {
            $attributes = $livewire->getOwnerRecord()
                ?->globalProduct
                ?->category
                ?->attributes
                ?->keyBy('id') ?? collect();
        } else {
            $tenantProductId = $livewire->data['tenant_product_id'] ?? null;
            if (! $tenantProductId) {
                return $attributes = collect();
            }

            $tenantProduct = TenantProduct::with('globalProduct.category.attributes')->find($tenantProductId);

            $attributes = $tenantProduct
                ?->globalProduct
                ?->category
                ?->attributes
                ?->keyBy('id') ?? collect();
        }

        return $attributes;
    }

    private static function getAttribute(Get $get, Component $livewire)
    {
        return self::getCategoryAttributes($livewire)->get($get('attribute_id'));
    }

    private static function type(Get $get, Component $livewire)
    {
        return self::getAttribute($get, $livewire)?->type;
    }

    private static function value($attributeId, Component $livewire)
    {
        if (! $attributeId) {
            return [];
        }

        return self::getCategoryValues($livewire)->get($attributeId)?->pluck('value', 'id') ?? [];
    }

    private static function getCategoryValues(Component $livewire)
    {
        static $values = null;

        if ($values !== null) {
            return $values;
        }

        $attributeIds = self::getCategoryAttributes($livewire)->keys();

        return $values = $attributeIds->isEmpty()
            ? collect()
            : AttributeValue::whereIn('attribute_id', $attributeIds)->get()->groupBy('attribute_id');
    }
}
