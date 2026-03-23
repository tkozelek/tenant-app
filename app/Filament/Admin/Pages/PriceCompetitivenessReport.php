<?php

namespace App\Filament\Admin\Pages;

use App\Models\GlobalProduct;
use App\Models\PriceHistory;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
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
            ->query(
                GlobalProduct::query()
                    ->withCount([
                        // count tenant_id co maju v ponuke
                        'tenantProducts as tenant_count' => fn ($query) => $query->select(DB::raw('count(distinct tenant_id)')),
                    ])
                    ->withMin('variants as min_price', 'price')
                    ->withMax('variants as max_price', 'price')
                    ->withAvg('variants as avg_price', 'price')
                    ->addSelect([
                        // bublame hore
                        // price history - variants - tenant products - global_prod.id
                        'price_change_count' => PriceHistory::selectRaw('count(*)')
                            ->join('tenant_product_variants', 'tenant_product_variants.id', '=', 'price_history.tenant_product_variant_id')
                            ->join('tenant_products', 'tenant_products.id', '=', 'tenant_product_variants.tenant_product_id')
                            ->whereColumn('tenant_products.global_product_id', 'global_products.id'),
                    ])
                    ->has('variants')
            )
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

    public function getView(): string
    {
        return 'filament.admin.pages.price-competitiveness-report';
    }
}
