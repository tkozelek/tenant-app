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
    /**
     * Build an attributes repeater section.
     *
     * @param  string  $relationship  The HasMany relationship name on the record (e.g. 'variantAttributes' or 'globalProductAttributes').
     * @param  callable(\Illuminate\Livewire\Component): int|null  $categoryIdResolver  Returns the category ID given the Livewire component.
     */
    public static function make(string $relationship, callable $categoryIdResolver): Section
    {
        return Section::make('Atribúty a parametre')
            ->description('Vyberte atribúty a nastavte im hodnoty.')
            ->schema([
                Repeater::make($relationship)
                    ->relationship($relationship)
                    ->label('')
                    ->defaultItems(0)
                    ->addActionLabel('Pridať atribút')
                    ->schema([
                        Select::make('attribute_id')
                            ->label('Atribút')
                            ->options(fn ($livewire) => self::attributeOptions($categoryIdResolver($livewire)))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('attribute_value_id', null);
                                $set('custom_value', null);
                            })
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        Select::make('attribute_value_id')
                            ->label('Hodnota')
                            ->options(fn (Get $get, $livewire) => self::valueOptions($get('attribute_id'), $categoryIdResolver($livewire)))
                            ->visible(fn (Get $get, $livewire) => self::attributeType($get('attribute_id'), $categoryIdResolver($livewire)) === 'select')
                            ->required(fn (Get $get, $livewire) => self::attributeType($get('attribute_id'), $categoryIdResolver($livewire)) === 'select'),

                        TextInput::make('custom_value')
                            ->label(fn (Get $get, $livewire) => 'Hodnota '.self::resolveAttribute($get('attribute_id'), $categoryIdResolver($livewire))?->unit)
                            ->visible(fn (Get $get, $livewire) => self::attributeType($get('attribute_id'), $categoryIdResolver($livewire)) !== 'select')
                            ->required(fn (Get $get, $livewire) => self::attributeType($get('attribute_id'), $categoryIdResolver($livewire)) !== 'select')
                            ->numeric(fn (Get $get, $livewire) => self::attributeType($get('attribute_id'), $categoryIdResolver($livewire)) === 'number')
                            ->helperText(fn (Get $get, $livewire) => self::attributeType($get('attribute_id'), $categoryIdResolver($livewire)) === 'bool' ? 'Zadajte 1 alebo 0' : null),
                    ])
                    ->columns()
                    ->columnSpanFull(),
            ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** @return array<int, string> */
    private static function attributeOptions(?int $categoryId): array
    {
        if (! $categoryId) {
            return [];
        }

        return Attribute::whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /** @return array<int, string> */
    private static function valueOptions(?int $attributeId, ?int $categoryId): array
    {
        if (! $attributeId || ! $categoryId) {
            return [];
        }

        return AttributeValue::where('attribute_id', $attributeId)
            ->orderBy('sort_order')
            ->pluck('value', 'id')
            ->all();
    }

    private static function resolveAttribute(?int $attributeId, ?int $categoryId): ?Attribute
    {
        if (! $attributeId || ! $categoryId) {
            return null;
        }

        return Attribute::find($attributeId);
    }

    private static function attributeType(?int $attributeId, ?int $categoryId): ?string
    {
        return self::resolveAttribute($attributeId, $categoryId)?->type;
    }
}
