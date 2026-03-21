<?php

namespace App\Filament\Admin\Widgets;

use App\Models\GlobalProduct;
use App\Models\GlobalProductRequest;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        return [
            Stat::make('Celkový počet tenantov', Tenant::count())
                ->description('Aktívny tenanti')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary'),

            Stat::make('Registrovaní používatelia', User::count())
                ->description('Všetci používatelia v systéme')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Nevyriešené žiadosti', GlobalProductRequest::where('status', 'pending')->count())
                ->description('Žiadosti čakajúce na vyriešenie')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('danger'),

            Stat::make('Globálne produkty', GlobalProduct::count())
                ->description('Počet schválených produktov v katalógu')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('info'),

            Stat::make('Tenant produkty', TenantProduct::count())
                ->description('Počet produktov u tenantov')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make('Varianty produktov', TenantProductVariant::count())
                ->description('Všetky vytvorené skladové varianty (SKU)')
                ->descriptionIcon('heroicon-m-swatch')
                ->color('gray'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->can('platform.access');
    }
}
