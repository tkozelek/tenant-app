<?php

namespace App\Filament\Tables;

use App\Models\TenantProductVariant;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class StockHistoryTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public TenantProductVariant $record;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->stockHistories()->getQuery())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Datum')
                    ->dateTime('d.m.Y H:i'),

                TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'purchase' => 'Nakup',
                        'sale' => 'Predaj',
                        'adjustment' => 'Oprava',
                        'return' => 'Vratenie',
                        'transfer' => 'Prevod',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'purchase' => 'success',
                        'sale' => 'danger',
                        'adjustment' => 'warning',
                        'return' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('quantity')
                    ->label('Množstvo')
                    ->numeric()
                    ->weight('bold')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('user.last_name')
                    ->limit(10)
                    ->label('Vykonal')
                    ->tooltip(fn ($record): string => "{$record->user?->first_name} {$record->user?->last_name} - {$record->user?->email}")
                    ->default('-'),

                TextColumn::make('note')
                    ->label('Poznámka')
                    ->default('-'),
            ])->paginated([5, 10, 25, 50])->defaultPaginationPageOption(10);
    }

    public function render(): View
    {
        return view('livewire.stock-history-table');
    }
}
