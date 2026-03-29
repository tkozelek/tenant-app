<?php

namespace App\Filament\Tenant\Pages;

use App\Models\TenantProductVariant;
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

class PriceMovementReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Pohyby cien';

    protected static string|\UnitEnum|null $navigationGroup = 'Reporty';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();

        return $user?->hasPermissionToOnTenant('tenant.reports.price_movement', $tenant) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Variant')
                    ->searchable()
                    ->tooltip(fn ($record) => $record->name)
                    ->limit(20)
                    ->weight('bold'),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->copyable()
                    ->limit(12)
                    ->color('gray'),

                TextColumn::make('current_price')
                    ->label('Cena')
                    ->money('EUR')
                    ->sortable()
                    ->color('primary'),

                TextColumn::make('change_count')
                    ->label('Zmeny')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('min_price')
                    ->label('Min')
                    ->money('EUR')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('max_price')
                    ->label('Max')
                    ->money('EUR')
                    ->sortable()
                    ->color('danger'),

                TextColumn::make('price_range')
                    ->label('Rozsah')
                    ->state(fn ($record): string => number_format((float) $record->price_range, 2, ',', ' ').' €')
                    ->sortable()
                    ->color(fn ($record): string => (float) $record->price_range > 20 ? 'danger' : 'warning'),

                TextColumn::make('trend')
                    ->headerTooltip('Prva cena - posledna')
                    ->label('Trend')
                    ->state(fn ($record): string => match (true) {
                        $record->first_price === null || $record->last_price === null => '-',
                        (float) $record->last_price > (float) $record->first_price => '+ '.number_format((float) $record->last_price - (float) $record->first_price, 2, ',', ' ').' €',
                        (float) $record->last_price < (float) $record->first_price => '- '.number_format((float) $record->first_price - (float) $record->last_price, 2, ',', ' ').' €',
                        default => 'bez zmeny',
                    })
                    ->badge()
                    ->color(fn ($record): string => match (true) {
                        $record->first_price === null || $record->last_price === null => 'gray',
                        (float) $record->last_price > (float) $record->first_price => 'danger',
                        (float) $record->last_price < (float) $record->first_price => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('last_changed_at')
                    ->label('Posledna zmena')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('activity')
                    ->label('Aktivita')
                    ->options([
                        '7' => 'Poslednych 7 dni',
                        '30' => 'Poslednych 30 dni',
                        '90' => 'Poslednych 90 dni',
                    ])
                    ->query(function ($query, array $data): void {
                        if (! empty($data['value'])) {
                            $query->where('ph.created_at', '>=', now()->subDays((int) $data['value']));
                        }
                    }),
            ])
            ->defaultSort('change_count', 'desc');
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $tenantId = Filament::getTenant()->id;

        return TenantProductVariant::query()
            ->select('tenant_product_variants.*')
            ->selectRaw(
                'COUNT(ph.id) as change_count,
                MIN(ph.price) as min_price,
                MAX(ph.price) as max_price,
                MAX(ph.price) - MIN(ph.price) as price_range,
                MAX(ph.created_at) as last_changed_at,
                (SELECT ph2.price FROM price_history ph2 WHERE ph2.tenant_product_variant_id = tenant_product_variants.id ORDER BY ph2.created_at ASC LIMIT 1) as first_price,
                (SELECT ph3.price FROM price_history ph3 WHERE ph3.tenant_product_variant_id = tenant_product_variants.id ORDER BY ph3.created_at DESC LIMIT 1) as last_price'
            )
            ->join('price_history as ph', 'tenant_product_variants.id', '=', 'ph.tenant_product_variant_id')
            ->join('tenant_products as tp', 'tp.id', '=', 'tenant_product_variants.tenant_product_id')
            ->where('tp.tenant_id', $tenantId)
            ->groupBy('tenant_product_variants.id')
            ->havingRaw('COUNT(ph.id) >= 2');
    }

    public function getView(): string
    {
        return 'filament.tenant.pages.price-movement-report';
    }
}
