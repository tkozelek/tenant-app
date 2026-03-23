<?php

namespace App\Providers\Filament;

use App\Filament\Tenant\Widgets\TenantDashboardStats;
use App\Filament\Tenant\Widgets\TenantStockOverviewChart;
use App\Http\Middleware\SetPermissionsTeamId;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant')
            ->path('tenant')
            ->tenant(Tenant::class, slugAttribute: 'slug')
            ->searchableTenantMenu()
            ->brandName(function (): string {
                $tenantId = getPermissionsTeamId();
                return Tenant::find($tenantId)?->name ?? 'Tenant';
            })
            ->searchableTenantMenu()
            ->homeUrl(fn() => route('dashboard.index'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->databaseNotifications()
            ->discoverResources(in: app_path('Filament/Tenant/Resources'), for: 'App\Filament\Tenant\Resources')
            ->discoverPages(in: app_path('Filament/Tenant/Pages'), for: 'App\Filament\Tenant\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Tenant/Widgets'), for: 'App\Filament\Tenant\Widgets')
            ->widgets([
                AccountWidget::class,
                TenantDashboardStats::class,
                TenantStockOverviewChart::class,
            ])
            ->userMenuItems([
                Action::make('backToApp')
                    ->label('Späť na aplikáciu')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->url(fn (): string => route('dashboard.index')),
            ])
            ->tenantMiddleware([
                SetPermissionsTeamId::class,
            ], isPersistent: true)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
