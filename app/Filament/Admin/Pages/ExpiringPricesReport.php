<?php

namespace App\Filament\Admin\Pages;

use App\Models\PriceHistory;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpiringPricesReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.admin.pages.expiring-prices-report';

    protected static string|\UnitEnum|null $navigationGroup = 'Reporty';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        return $user?->hasPermissionTo('reports.expiring_prices') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PriceHistory::query()
                    ->whereNotNull('valid_to')
                    ->where('valid_to', '>', now())
                    ->where('valid_to', '<=', now()->addDays(7))
                    ->with(['variant.product.tenant', 'user'])
            )
            ->columns([
                TextColumn::make('variant.product.tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('variant.product.name')
                    ->label('Produkt')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('variant.name')
                    ->label('Variant')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('variant.sku')
                    ->label('SKU')
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('price')
                    ->label('Cena')
                    ->state(fn (PriceHistory $record): string => number_format((float) $record->price, 2, ',', ' ').' €')
                    ->weight('bold'),

                TextColumn::make('original_price')
                    ->label('Povodna cena')
                    ->state(fn (PriceHistory $record): string => $record->original_price
                        ? number_format((float) $record->original_price, 2, ',', ' ').' €'
                        : '-'
                    )
                    ->color('gray'),

                TextColumn::make('valid_to')
                    ->label('Konci')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color(fn (PriceHistory $record): string => $record->valid_to->lt(now()->addDays(2))
                        ? 'danger'
                        : 'warning'
                    )
                    ->badge(),

                TextColumn::make('days_left')
                    ->label('Zostatok')
                    ->state(fn (PriceHistory $record): string => now()->diffInHours($record->valid_to) < 24
                        ? now()->diffInHours($record->valid_to).'h'
                        : now()->diffInDays($record->valid_to).' dni'
                    )
                    ->badge()
                    ->color(fn (PriceHistory $record): string => $record->valid_to->lt(now()->addDays(2))
                        ? 'danger'
                        : 'warning'
                    ),
            ])
            ->filters([
                SelectFilter::make('tenant')
                    ->label('Tenant')
                    ->relationship('variant.product.tenant', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('days')
                    ->label('Expiruje za')
                    ->options([
                        '1' => 'Do 24 hodin',
                        '3' => 'Do 3 dni',
                        '7' => 'Do 7 dni',
                    ])
                    ->query(fn ($query, array $data) => $data['value']
                        ? $query->where('valid_to', '<=', now()->addDays((int) $data['value']))
                        : $query
                    ),
            ])
            ->defaultSort('valid_to');
    }
}
