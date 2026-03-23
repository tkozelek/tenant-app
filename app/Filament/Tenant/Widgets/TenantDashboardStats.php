<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\GlobalProductRequest;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class TenantDashboardStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();

        $productCount = TenantProduct::where('tenant_id', $tenant->id)->count();
        $activeProductCount = TenantProduct::where('tenant_id', $tenant->id)->where('is_active', true)->count();

        $variantCount = TenantProductVariant::whereHas(
            'product',
            fn (Builder $q) => $q->where('tenant_id', $tenant->id)
        )->count();

        $totalStock = TenantProductVariant::whereHas(
            'product',
            fn (Builder $q) => $q->where('tenant_id', $tenant->id)
        )->sum('stock_quantity');

        $lowStockCount = TenantProductVariant::whereHas(
            'product',
            fn (Builder $q) => $q->where('tenant_id', $tenant->id)
        )->where('stock_quantity', '<=', 5)->where('stock_quantity', '>', 0)->count();

        $pendingRequests = GlobalProductRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->count();

        return [
            Stat::make('Produkty', $productCount)
                ->description("{$activeProductCount} aktívnych")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Varianty (SKU)', $variantCount)
                ->description('Celkový počet skladových položiek')
                ->descriptionIcon('heroicon-m-swatch')
                ->color('info'),

            Stat::make('Skladom kusov', number_format((int) $totalStock))
                ->description($lowStockCount > 0 ? "{$lowStockCount} položiek s nízkym stavom" : 'Všetky zásoby v poriadku')
                ->descriptionIcon($lowStockCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($lowStockCount > 0 ? 'warning' : 'success'),

            Stat::make('Čakajúce žiadosti', $pendingRequests)
                ->description('Žiadosti o nové produkty')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color($pendingRequests > 0 ? 'danger' : 'gray'),
        ];
    }
}
