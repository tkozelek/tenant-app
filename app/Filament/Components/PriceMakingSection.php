<?php

namespace App\Filament\Components;

use App\Filament\Admin\Resources\TenantProductVariants\Schemas\actions\QuantityPriceRepeater;
use App\Models\TenantProductVariant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

class PriceMakingSection
{
    public static function make(): Section
    {
        return Section::make('Cenotvorba')
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
            ]);
    }
}
