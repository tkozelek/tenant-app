<?php

namespace App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class AdjustPriceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'adjust_price';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Cena')
            ->icon('heroicon-o-currency-euro')
            ->color('success')
            ->schema([
                TextInput::make('price')
                    ->label('Nova cena')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->step('0.01'),

                TextInput::make('original_price')
                    ->label('Povodna cena (pred zlavou)')
                    ->numeric()
                    ->prefix('€')
                    ->step('0.01')
                    ->helperText('Vyplnte, ak je produkt v zlave.'),

                DateTimePicker::make('valid_from')
                    ->label('Platne od')
                    ->default(now())
                    ->required()
                    ->helperText('Pre okamzitu zmenu nechajte aktualny cas. Pre naplanovanу zmenu nastavte buduci datum.'),

                DateTimePicker::make('valid_to')
                    ->label('Platne do')
                    ->after('valid_from')
                    ->helperText('Nepovinne. Ak je vyplnene, cena sa automaticky deaktivuje po tomto datume.'),
            ])
            ->action(function (array $data, $record): void {
                try {
                    DB::transaction(function () use ($record, $data): void {
                        $record->priceHistories()->create([
                            'price' => $data['price'],
                            'original_price' => $data['original_price'] ?? null,
                            'valid_from' => Carbon::parse($data['valid_from']),
                            'valid_to' => isset($data['valid_to']) ? Carbon::parse($data['valid_to']) : null,
                            'user_id' => auth()->id(),
                        ]);
                    });

                    Notification::make()->title('Cena zmenena uspesne.')->success()->send();
                } catch (\Exception $e) {
                    Notification::make()->title('Nastala chyba.')->danger()->send();
                }
            });
    }
}
