<?php

namespace App\Filament\Tenant\Pages;

use App\Enums\StockHistoryType;
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

class StockHealthReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Zdravie skladu';

    protected static ?string $navigationLabel = 'Zdravie skladu';

    protected static string|\UnitEnum|null $navigationGroup = 'Reporty';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();

        return $user?->hasPermissionToOnTenant('tenant.reports.stock_health', $tenant) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TenantProductVariant::query()
                    ->whereHas('product', fn ($q) => $q->where('tenant_id', Filament::getTenant()?->id))
                    ->with('product')
                    ->withSum(['stockHistories as stock_in' => fn ($q) => $q->where('type', StockHistoryType::Purchase)], 'quantity')
                    ->withSum(['stockHistories as stock_out' => fn ($q) => $q->where('type', StockHistoryType::Sale)], 'quantity')
                    ->withSum(['stockHistories as stock_other' => fn ($q) => $q->whereIn('type', [
                        StockHistoryType::Adjustment,
                        StockHistoryType::Return,
                        StockHistoryType::Transfer,
                    ])], 'quantity')
                    ->withCount('stockHistories as movement_count')
                    ->orderBy('stock_quantity')
            )
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

                TextColumn::make('stock_quantity')
                    ->label('Aktualne')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->stock_quantity === 0 => 'danger',
                        $record->stock_quantity <= 5 => 'warning',
                        $record->stock_quantity > 100 => 'info',
                        default => 'success',
                    }),

                TextColumn::make('status')
                    ->label('Stav')
                    ->state(fn ($record) => match (true) {
                        $record->stock_quantity === 0 => 'Vypredane',
                        $record->stock_quantity <= 5 => 'Kriticke',
                        $record->stock_quantity > 100 => 'Extra',
                        default => 'V poriadku',
                    })
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->stock_quantity === 0 => 'danger',
                        $record->stock_quantity <= 5 => 'warning',
                        $record->stock_quantity > 100 => 'info',
                        default => 'success',
                    }),

                TextColumn::make('stock_in')
                    ->label('Celkovo prijate')
                    ->state(fn ($record) => ($record->stock_in ?? 0))
                    ->suffix(' ks')
                    ->color('success')
                    ->alignCenter(),

                TextColumn::make('stock_out')
                    ->label('Celkovo vydane')
                    ->state(fn ($record) => ($record->stock_out ?? 0))
                    ->suffix(' ks')
                    ->color('danger')
                    ->alignCenter(),

                TextColumn::make('stock_other')
                    ->label('Ostatne pohyby')
                    ->state(function ($record): string {
                        $value = (int) ($record->stock_other ?? 0);

                        return $value === 0 ? '0 ks' : sprintf('%+d ks', $value);
                    })
                    ->color(fn ($record): string => match (true) {
                        (int) ($record->stock_other ?? 0) > 0 => 'success',
                        (int) ($record->stock_other ?? 0) < 0 => 'danger',
                        default => 'gray',
                    })
                    ->alignCenter(),

                TextColumn::make('movement_count')
                    ->label('Pohyby skladu')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('current_price')
                    ->label('Cena')
                    ->state(fn ($record) => $record->current_price_formatted),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav skladu')
                    ->options([
                        'out' => 'Vypredane',
                        'critical' => 'Kriticke (<=5)',
                        'extra' => 'Extra (>100)',
                        'ok' => 'V poriadku',
                    ])
                    ->query(fn ($query, array $data) => match ($data['value'] ?? null) {
                        'out' => $query->where('stock_quantity', 0),
                        'critical' => $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5),
                        'extra' => $query->where('stock_quantity', '>', 100),
                        'ok' => $query->where('stock_quantity', '>', 5)->where('stock_quantity', '<=', 100),
                        default => $query,
                    }),
            ])
            ->defaultSort('stock_quantity');
    }

    public function getView(): string
    {
        return 'filament.tenant.pages.stock-health-report';
    }
}
