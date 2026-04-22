<?php

namespace App\Filament\Components;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ProductAttributesSection
{
    public static function make(string $relationship, callable $categoryIdResolver, bool $multipleValues = false): Section
    {
        $attributeSelector = Select::make('attribute_id')
            ->label('Atribút')
            ->options(fn ($livewire) => self::attributeOptions($categoryIdResolver($livewire))) // ziska z parenta kategoriu,
            ->required()
            ->live()
            ->afterStateUpdated(function (Set $set) {
                $set('attribute_value_id', null);
                $set('custom_value', null);
            });

        if ($multipleValues) {
            $attributeSelector->disableOptionsWhenSelectedInSiblingRepeaterItems();
        }

        $repeater = Repeater::make($relationship)
            ->label('')
            ->defaultItems(0)
            ->addActionLabel('Pridať atribút')
            ->schema([
                $attributeSelector,

                Select::make('attribute_value_id')
                    ->label('Hodnota')
                    ->options(fn (Get $get) => self::valueOptions($get('attribute_id')))
                    ->visible(fn (Get $get) => self::attributeType($get('attribute_id')) === 'select')
                    ->required(fn (Get $get) => self::attributeType($get('attribute_id')) === 'select')
                    ->multiple($multipleValues),

                TextInput::make('custom_value')
                    ->label(fn (Get $get) => 'Hodnota '.Attribute::find($get('attribute_id'))?->unit)
                    ->visible(fn (Get $get) => self::attributeType($get('attribute_id')) !== 'select')
                    ->required(fn (Get $get) => self::attributeType($get('attribute_id')) !== 'select')
                    ->numeric(fn (Get $get) => self::attributeType($get('attribute_id')) === 'number')
                    ->helperText(fn (Get $get) => self::attributeType($get('attribute_id')) === 'bool' ? 'Zadajte 1 alebo 0' : null),
            ])
            ->columns()
            ->columnSpanFull();

        if (! $multipleValues) {
            $repeater->relationship($relationship);
        }

        return Section::make('Atribúty a parametre')
            ->description('Vyberte atribúty a nastavte im hodnoty.')
            ->schema([$repeater]);
    }

    private static function attributeOptions(?int $categoryId): array
    {
        if (! $categoryId) {
            return [];
        }

        $category = Category::find($categoryId);

        if (! $category) {
            return [];
        }

        $categoryIds = $category->subtreeCategoryIds();

        return Attribute::whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    private static function valueOptions(?int $attributeId): array
    {
        if (! $attributeId) {
            return [];
        }

        return AttributeValue::where('attribute_id', $attributeId)
            ->orderBy('sort_order')
            ->pluck('value', 'id')
            ->all();
    }

    private static function attributeType(?int $attributeId): ?string
    {
        return Attribute::find($attributeId)?->type;
    }
}
