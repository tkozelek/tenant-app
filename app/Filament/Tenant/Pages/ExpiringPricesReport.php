<?php

namespace App\Filament\Tenant\Pages;

use App\Filament\Tenant\Resources\TenantProductVariants\TenantProductVariantResource;
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

    protected static string|\UnitEnum|null $navigationGroup = 'Reporty';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();

        return $user?->hasPermissionToOnTenant('tenant.reports.expiring_prices', $tenant) ?? false;
    }

    public function table(Table $table): Table
    {
        $tenantId = Filament::getTenant()->id;

        return $table
            ->query(
                PriceHistory::query()
                    ->whereNotNull('valid_to')
                    ->where('valid_to', '>', now())
                    ->where('valid_to', '<=', now()->addDays(7))
                    ->whereHas('variant.product', fn ($q) => $q->where('tenant_id', $tenantId))
                    ->with(['variant.product', 'user'])
            )
            ->columns([
                TextColumn::make('variant.product.name')
                    ->label('Produkt')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('variant.name')
                    ->label('Variant')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->url(fn ($record) => TenantProductVariantResource::getUrl('edit', ['record' => $record->tenant_product_variant_id])),

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

                TextColumn::make('valid_from')
                    ->label('Platna od')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

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
                        ? number_format(now()->diffInHours($record->valid_to), 2).'h'
                        : number_format(now()->diffInDays($record->valid_to), 2).' dni'
                    )
                    ->badge()
                    ->color(fn (PriceHistory $record): string => $record->valid_to->lt(now()->addDays(2))
                        ? 'danger'
                        : 'warning'
                    ),
            ])
            ->filters([
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

    public function getView(): string
    {
        return 'filament.tenant.pages.expiring-prices-report';
    }
}
