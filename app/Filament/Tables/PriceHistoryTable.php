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
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Livewire\Component;

class PriceHistoryTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions, InteractsWithForms, InteractsWithTable;

    public Model $record;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->priceHistories()->getQuery())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Dátum zmeny')
                    ->dateTime('d.m.Y H:i'),

                TextColumn::make('price')
                    ->label('Nová cena')
                    ->money('EUR')
                    ->weight('bold'),

                TextColumn::make('valid_from')
                    ->label('Platné od')
                    ->dateTime('d.m.Y H:i'),

                TextColumn::make('valid_to')
                    ->label('Platné do')
                    ->default('Aktuálne')
                    ->badge(fn ($state) => $state === null),

                TextColumn::make('user.email')
                    ->label('Zmenil')
                    ->default('Systém'),
            ])->paginated([5, 10, 25, 50])->defaultPaginationPageOption(10);
    }

    public function render(): View
    {
        return view('livewire.price-history-table');
    }
}
