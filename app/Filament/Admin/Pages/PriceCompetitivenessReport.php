<?php

namespace App\Filament\Admin\Pages;

use App\Models\GlobalProduct;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PriceCompetitivenessReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Cenove rozpetie';

    protected static ?string $navigationLabel = 'Cenove rozpetie';

    protected static string|\UnitEnum|null $navigationGroup = 'Reporty';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Produkt')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('category.name')
                    ->label('Kategoria')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('tenant_count')
                    ->label('Pocet predajcov')
                    ->badge()
                    ->color('info')
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

                TextColumn::make('spread')
                    ->label('Rozdiel')
                    ->state(fn ($record) => $record->max_price && $record->min_price
                        ? number_format((float) $record->max_price - (float) $record->min_price, 2).' €'
                        : '-'
                    )
                    ->color(fn ($record) => $record->max_price && $record->min_price && ((float) $record->max_price - (float) $record->min_price) > 50
                        ? 'warning'
                        : null
                    ),

                TextColumn::make('avg_price')
                    ->label('Priemer')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('price_change_count')
                    ->label('Zmeny ()x')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategoria')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->multiple(),
            ])
            ->defaultSort('name');
    }

    protected function getTableQuery(): Builder
    {
        $priceStats = DB::table('price_history as ph')
            ->selectRaw('
                tp.global_product_id,
                MIN(ph.price) as min_price,
                MAX(ph.price) as max_price,
                AVG(ph.price) as avg_price,
                COUNT(ph.id) as price_change_count,
                COUNT(DISTINCT tp.tenant_id) as tenant_count
            ')
            ->join('tenant_product_variants as tpv', 'tpv.id', '=', 'ph.tenant_product_variant_id')
            ->join('tenant_products as tp', 'tp.id', '=', 'tpv.tenant_product_id')
            ->where('ph.valid_from', '<=', now())
            ->where(fn ($q) => $q->whereNull('ph.valid_to')->orWhere('ph.valid_to', '>=', now()))
            ->groupBy('tp.global_product_id');

        return GlobalProduct::query()
            ->select('global_products.*')
            ->leftJoinSub($priceStats, 'price_stats', 'global_products.id', '=', 'price_stats.global_product_id')
            ->whereNotNull('price_stats.min_price');
    }

    public function getView(): string
    {
        return 'filament.admin.pages.price-competitiveness-report';
    }
}
