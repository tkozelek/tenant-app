<?php

namespace App\Filament\Resources\TenantProductVariants\Schemas\actions;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

// 1. FIXED: Correct namespace for Get

class QuantityPriceRepeater
{
    public static function make(): Repeater
    {
        return Repeater::make('quantityPrices')
            ->relationship()
            ->label('Množstevné ceny (Prepisujú základnú cenu)')
            ->addActionLabel('Pridať množstevnú cenu')
            ->collapsible()
            ->defaultItems(0)
            ->reorderable(false)
            ->itemLabel(function (array $state): ?string {
                $minQuantity = $state['min_quantity'] ?? 0;
                $maxQuantity = $state['max_quantity'] ?? "";

                return sprintf("%s ks - %s => %s €", $minQuantity, $maxQuantity, $state['unit_price']);
            })
            ->rules([
                fn () => function (string $attribute, $value, \Closure $fail) {
                    if (!is_array($value) || empty($value)) return;

                    $tiers = array_values(array_filter($value, fn($item) => is_array($item)));

                    if (empty($tiers)) return;

                    usort($tiers, function ($a, $b) {
                        $aMin = $a['min_quantity'] ?? 0;
                        $bMin = $b['min_quantity'] ?? 0;

                        if ($aMin == $bMin) {
                            return 0;
                        }

                        return ($aMin < $bMin) ? -1 : 1;
                    });

                    $totalItems = count($tiers);
                    $previousMax = null;

                    foreach ($tiers as $index => $item) {
                        $min = (int) ($item['min_quantity'] ?? 0);
                        $max = !empty($item['max_quantity']) ? (int) $item['max_quantity'] : null;
                        $isLast = ($index === $totalItems - 1);

                        // last one
                        if ($isLast && $max !== null) {
                            $fail("Posledná cenová hladina (od $min ks) musí mať 'Maximálny počet' prázdny (a viac).");
                        }

                        if (!$isLast && $max === null) {
                            $fail("Cenová hladina (od $min ks) nie je posledná, preto musí mať vyplnený 'Maximálny počet'.");
                        }

                        if ($previousMax !== null) {
                            if ($min <= $previousMax) {
                                $fail("Rozsahy sa nesmú prekrývať.");
                            }
                            if ($min > $previousMax + 1) {
                                $fail("Medzi rozsahmi nesmú byť medzery (chýbajúce množstvo medzi {$previousMax} a {$min} ks).");
                            }
                        }
                        $previousMax = $max;
                    }
                },
            ])
            ->schema([
                TextInput::make('min_quantity')
                    ->label('Minimálny počet (ks)')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->columnSpan(1)
                    ->default(function (Get $get) {
                        $repeaterState = $get('../../quantityPrices');

                        if (!is_array($repeaterState) || empty($repeaterState)) {
                            return 1;
                        }

                        $validTiers = array_filter($repeaterState, fn($item) => is_array($item) && !empty($item['min_quantity']));
                        if (empty($validTiers)) {
                            return 1;
                        }

                        usort($validTiers, fn($a, $b) => ((int)($a['min_quantity'] ?? 0)) <=> ((int)($b['min_quantity'] ?? 0)));
                        $lastItem = end($validTiers);

                        if (!empty($lastItem['max_quantity'])) {
                            return (int) $lastItem['max_quantity'] + 1;
                        }

                        return (int) $lastItem['min_quantity'] + 1;
                    }),

                TextInput::make('max_quantity')
                    ->label('Maximálny počet (ks)')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Povinné, ak pridávate ďalšiu úroveň.')
                    ->columnSpan(1)
                    ->rules([
                        fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            $min = $get('min_quantity');
                            if ($value !== null && $value !== '' && $min !== null && (int)$value <= (int)$min) {
                                $fail('Maximálny počet musí byť väčší ako minimálny počet.');
                            }
                        },
                    ]),

                TextInput::make('unit_price')
                    ->label('Cena za kus')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->step('0.01')
                    ->columnSpan(1),
            ])
            ->columns(3)
            ->columnSpanFull();
    }
}
