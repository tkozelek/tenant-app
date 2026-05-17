<?php

namespace App\Filament\Tenant\Pages;

use App\Filament\Tenant\Resources\TenantProductVariants\TenantProductVariantResource;
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
use Illuminate\Database\Eloquent\Builder;

class StaleProductsReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Neaktualizovane produkty';

    protected static ?string $navigationLabel = 'Neaktualizovane produkty';

    protected static string|\UnitEnum|null $navigationGroup = 'Reporty';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();

        return $user?->hasPermissionToOnTenant('tenant.reports.stale_products', $tenant) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Variant')
                    ->searchable()
                    ->weight('bold')
                    ->url(fn ($record) => TenantProductVariantResource::getUrl('edit', ['record' => $record->id])),

                TextColumn::make('product.name')
                    ->label('Produkt')
                    ->badge()
                    ->color('info'),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('current_price')
                    ->label('Cena')
                    ->state(fn ($record) => $record->current_price_formatted),

                TextColumn::make('stock_quantity')
                    ->label('Sklad')
                    ->badge()
                    ->alignCenter()
                    ->color(fn ($record): string => match (true) {
                        $record->stock_quantity === 0 => 'danger',
                        $record->stock_quantity <= 5 => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('last_price_change')
                    ->label('Posledna zmena ceny')
                    ->dateTime('d.m.Y')
                    ->state(fn ($record): string => $record->last_price_change ? $record->last_price_change : 'Nikdy')
                    ->color('gray'),

                TextColumn::make('last_stock_movement')
                    ->label('Posledny pohyb skladu')
                    ->dateTime('d.m.Y')
                    ->state(fn ($record): string => $record->last_stock_movement ? $record->last_stock_movement : 'Nikdy')
                    ->color('gray'),

                TextColumn::make('last_activity')
                    ->label('Posledna aktivita')
                    ->sortable()
                    ->dateTime('d.m.Y')
                    ->color(fn ($record): string => match (true) {
                        $record->last_activity <= now()->subDays(90)->toDateTimeString() => 'danger',
                        $record->last_activity <= now()->subDays(30)->toDateTimeString() => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('threshold')
                    ->label('Bez aktivity viac ako')
                    ->options([
                        '14' => '14 dni',
                        '30' => '30 dni',
                        '60' => '60 dni',
                        '90' => '90 dni',
                        '180' => '180 dni',
                    ])
                    ->query(function (Builder $query, array $data): void {
                        if (! empty($data['value'])) {
                            $days = (int) $data['value'];
                            $query->havingRaw('GREATEST(
                                COALESCE(MAX(ph.created_at), tenant_product_variants.updated_at),
                                COALESCE(MAX(sh.created_at), tenant_product_variants.updated_at),
                                tenant_product_variants.updated_at
                            ) <= ?', [now()->subDays($days)]);
                        }
                    }),
            ])
            ->defaultSort('last_activity', 'asc');
    }

    protected function getTableQuery(): Builder
    {
        $tenantId = Filament::getTenant()->id;

        return TenantProductVariant::query()
            ->select('tenant_product_variants.*')
            ->selectRaw('
                MAX(ph.created_at) as last_price_change,
                MAX(sh.created_at) as last_stock_movement,
                GREATEST(
                    COALESCE(MAX(ph.created_at), tenant_product_variants.updated_at),
                    COALESCE(MAX(sh.created_at), tenant_product_variants.updated_at),
                    tenant_product_variants.updated_at
                ) as last_activity
            ')
            ->leftJoin('price_history as ph', 'ph.tenant_product_variant_id', '=', 'tenant_product_variants.id')
            ->leftJoin('stock_history as sh', 'sh.product_variant_id', '=', 'tenant_product_variants.id')
            ->join('tenant_products as tp', 'tp.id', '=', 'tenant_product_variants.tenant_product_id')
            ->where('tp.tenant_id', $tenantId)
            ->with('product')
            ->groupBy('tenant_product_variants.id');
    }

    public function getView(): string
    {
        return 'filament.tenant.pages.stale-products-report';
    }
}
