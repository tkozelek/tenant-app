<?php

namespace App\Filament\Resources\TenantProductVariants\Schemas\actions;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

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
            ->itemLabel(fn (array $state): ?string =>
            isset($state['min_quantity']) && isset($state['unit_price'])
                ? "Od {$state['min_quantity']} ks - {$state['unit_price']} €" . (empty($state['max_quantity']) ? " (a viac)" : " (do {$state['max_quantity']} ks)")
                : null
            )
            ->rules([
                fn () => function (string $attribute, $value, \Closure $fail) {
                    if (!is_array($value) || empty($value)) return;

                    usort($value, function ($a, $b) {
                        $aMin = $a['min_quantity'] ?? 0;
                        $bMin = $b['min_quantity'] ?? 0;

                        if ($aMin == $bMin) {
                            return 0;
                        }

                        return ($aMin < $bMin) ? -1 : 1;
                    });

                    $totalItems = count($value);
                    $previousMax = null;
                    $previousMin = null;

                    foreach ($value as $index => $item) {
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

                        // overlap
                        if ($previousMax !== null && $min <= $previousMax) {
                            $fail("Rozsahy sa nesmú prekrývať.");
                        }

                        if ($previousMin !== null && $min <= $previousMin) {
                            $fail("Minimálny počet musí byť vždy väčší ako v predchádzajúcej hladine.");
                        }

                        $previousMin = $min;
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

                        if (empty($repeaterState)) {
                            return 1;
                        }

                        $lastItem = end($repeaterState);

                        if (!empty($lastItem['max_quantity'])) {
                            return (int) $lastItem['max_quantity'] + 1;
                        }

                        if (!empty($lastItem['min_quantity'])) {
                            return (int) $lastItem['min_quantity'] + 1;
                        }

                        return 1;
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
                            if ($value && $min && (int)$value <= (int)$min) {
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
