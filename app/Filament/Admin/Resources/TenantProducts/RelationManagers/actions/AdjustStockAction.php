<?php

namespace App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions;

use App\Enums\StockHistoryType;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class AdjustStockAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'adjust_stock';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Sklad')
            ->icon('heroicon-o-circle-stack')
            ->color('warning')
            ->schema([
                Select::make('type')
                    ->label('Zmenit typ')
                    ->options(StockHistoryType::options())
                    ->required(),
                TextInput::make('quantity')
                    ->label('Zmena skladu')
                    ->numeric()
                    ->required()
                    ->rules([
                        fn ($record) => function (string $attribute, $value, \Closure $fail) use ($record) {
                            $currentStock = $record->stock_quantity;

                            if ($value < 0 && ($currentStock + $value) < 0) {
                                $fail("Nedostatok zasob. Nemozete odobrat viac ako je aktuálny stav ({$currentStock}).");
                            }
                        },
                    ])
                    ->helperText('Pozitívne na pridanie kusov, negatívne na odobratie.'),
                Textarea::make('note')
                    ->label('Note')
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, $record) {
                // transakcia, keby nejaké zlyha
                try {
                    DB::transaction(function () use ($record, $data) {
                        $record->stockHistories()->create([
                            'type' => $data['type'],
                            'quantity' => $data['quantity'],
                            'note' => $data['note'],
                            'user_id' => auth()->user()->id ?? null,
                        ]);

                        $record->increment('stock_quantity', $data['quantity']);
                    });
                } catch (\Exception $e) {
                    Notification::make()->title('Nastala chyba.')->danger()->send();
                }
            })->successNotificationTitle('Stav skladu zmenený úspešne.');
    }
}
