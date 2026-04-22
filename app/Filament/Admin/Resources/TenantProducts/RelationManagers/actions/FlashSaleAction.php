<?php

namespace App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions;

use App\Models\TenantProductVariant;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class FlashSaleAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'flash_sale';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Flash sale')
            ->icon('heroicon-o-bolt')
            ->color(fn (TenantProductVariant $record) => $record->activePriceHistory?->is_flash_sale ? 'danger' : 'warning')
            ->fillForm(function (TenantProductVariant $record): array {
                $active = $record->activePriceHistory;
                $isFlashSale = $active?->is_flash_sale ?? false;

                return [
                    'flash_sale_label' => $isFlashSale ? $active->flash_sale_label : null,
                    'sale_price' => $isFlashSale ? (float) $active->price : null,
                    'original_price' => $isFlashSale ? (float) $active->original_price : (float) $active?->price,
                    'starts_at' => $isFlashSale ? $active->valid_from : now(),
                    'ends_at' => $isFlashSale ? $active->valid_to : null,
                ];
            })
            ->schema([
                TextInput::make('flash_sale_label')
                    ->label('Nazov akcie')
                    ->placeholder('Black Friday, Weekend sale...')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('sale_price')
                    ->label('Cena')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->step('0.01')
                    ->columnSpan(1),

                TextInput::make('original_price')
                    ->label('Preciarkuta cena')
                    ->numeric()
                    ->prefix('€')
                    ->step('0.01')
                    ->columnSpan(1),

                DateTimePicker::make('starts_at')
                    ->label('Od')
                    ->required()
                    ->columnSpan(1),

                DateTimePicker::make('ends_at')
                    ->label('Do')
                    ->required()
                    ->after('starts_at')
                    ->columnSpan(1),
            ])
            ->action(function (array $data, TenantProductVariant $record): void {
                $startsAt = Carbon::parse($data['starts_at']);
                $endsAt = Carbon::parse($data['ends_at']);

                try {
                    DB::transaction(function () use ($record, $data, $startsAt, $endsAt): void {
                        $current = $record->activePriceHistory;
                        $isFlashSale = $current?->is_flash_sale ?? false;

                        if ($isFlashSale) {
                            $current->update([
                                'price' => $data['sale_price'],
                                'original_price' => $data['original_price'] ?? null,
                                'valid_from' => $startsAt,
                                'valid_to' => $endsAt,
                                'flash_sale_label' => $data['flash_sale_label'] ?? null,
                            ]);

                            $record->priceHistories()
                                ->where('valid_from', $current->getOriginal('valid_to'))
                                ->where('is_flash_sale', false)
                                ->first()
                                ?->update(['valid_from' => $endsAt]);
                        } else {
                            $regularPrice = $current?->price ?? $data['sale_price'];

                            if ($current) {
                                $current->update(['valid_to' => $startsAt]);
                            }

                            $record->priceHistories()->create([
                                'price' => $data['sale_price'],
                                'original_price' => $data['original_price'] ?? null,
                                'valid_from' => $startsAt,
                                'valid_to' => $endsAt,
                                'is_flash_sale' => true,
                                'flash_sale_label' => $data['flash_sale_label'] ?? null,
                                'user_id' => auth()->id(),
                            ]);

                            $record->priceHistories()->create([
                                'price' => $regularPrice,
                                'original_price' => null,
                                'valid_from' => $endsAt,
                                'valid_to' => null,
                                'is_flash_sale' => false,
                                'user_id' => auth()->id(),
                            ]);
                        }
                    });

                    Notification::make()->title('Flash sale ulozeny.')->success()->send();
                } catch (\Exception $e) {
                    Notification::make()->title('Chyba.')->danger()->send();
                }
            });
    }
}
