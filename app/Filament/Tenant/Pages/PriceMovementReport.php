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
use Illuminate\Support\Facades\DB;

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
                    ->weight('bold'),

                TextColumn::make('product.name')
                    ->label('Produkt')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('change_count')
                    ->label('Pocet zmien')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('min_price')
                    ->label('Min cena')
                    ->money('EUR')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('max_price')
                    ->label('Max cena')
                    ->money('EUR')
                    ->sortable()
                    ->color('danger'),

                TextColumn::make('price_range')
                    ->label('Rozsah')
                    ->state(fn ($record): string => number_format((float) $record->price_range, 2, ',', ' ').' €')
                    ->sortable()
                    ->color(fn ($record): string => (float) $record->price_range > 20 ? 'danger' : 'warning'),

                TextColumn::make('trend')
                    ->label('Trend')
                    ->state(fn ($record): string => match (true) {
                        $record->first_price === null || $record->last_price === null => '-',
                        (float) $record->last_price > (float) $record->first_price => '+'.number_format((float) $record->last_price - (float) $record->first_price, 2, ',', ' ').' €',
                        (float) $record->last_price < (float) $record->first_price => '-'.number_format((float) $record->first_price - (float) $record->last_price, 2, ',', ' ').' €',
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
                    ->query(fn ($query, array $data) => $data['value']
                        ? $query->where('ps.last_changed_at', '>=', now()->subDays((int) $data['value']))
                        : $query
                    ),
            ])
            ->defaultSort('change_count', 'desc');
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $tenantId = Filament::getTenant()->id;

        $priceStats = DB::table('price_history as ph')
            ->selectRaw('
                ph.tenant_product_variant_id,
                COUNT(ph.id) as change_count,
                MIN(ph.price) as min_price,
                MAX(ph.price) as max_price,
                MAX(ph.price) - MIN(ph.price) as price_range,
                MAX(ph.created_at) as last_changed_at,
                (SELECT ph2.price FROM price_history ph2 WHERE ph2.tenant_product_variant_id = ph.tenant_product_variant_id ORDER BY ph2.created_at ASC LIMIT 1) as first_price,
                (SELECT ph3.price FROM price_history ph3 WHERE ph3.tenant_product_variant_id = ph.tenant_product_variant_id ORDER BY ph3.created_at DESC LIMIT 1) as last_price
            ')
            ->join('tenant_product_variants as tpv', 'tpv.id', '=', 'ph.tenant_product_variant_id')
            ->join('tenant_products as tp', 'tp.id', '=', 'tpv.tenant_product_id')
            ->where('tp.tenant_id', $tenantId)
            ->groupBy('ph.tenant_product_variant_id')
            ->havingRaw('COUNT(ph.id) >= 2');

        return TenantProductVariant::query()
            ->select('tenant_product_variants.*')
            ->selectRaw('ps.change_count, ps.min_price, ps.max_price, ps.price_range, ps.last_changed_at, ps.first_price, ps.last_price')
            ->joinSub($priceStats, 'ps', 'tenant_product_variants.id', '=', 'ps.tenant_product_variant_id')
            ->with(['product']);
    }

    public function getView(): string
    {
        return 'filament.tenant.pages.price-movement-report';
    }
}
