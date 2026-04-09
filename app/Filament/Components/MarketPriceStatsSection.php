<?php

namespace App\Filament\Components;

use App\Models\PriceHistory;
use App\Models\TenantProduct;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class MarketPriceStatsSection
{
    public static function make(): Section
    {
        return Section::make('Cenovy prehlad trhu')
            ->description('Aktualne aktivne ceny toho isteho produktu napriec predajcami')
            ->hidden(fn (Get $get): bool => blank($get('tenant_product_id')))
            ->schema([
                TextEntry::make('market_avg')
                    ->label('Priemer')
                    ->state(function (Get $get): string {
                        $stats = static::stats((int) $get('tenant_product_id'));

                        return $stats && $stats->total > 0
                            ? number_format((float) $stats->avg_price, 2, ',', ' ').' €'
                            : 'Ziadne data';
                    }),

                TextEntry::make('market_min')
                    ->label('Minimum')
                    ->state(function (Get $get): string {
                        $stats = static::stats((int) $get('tenant_product_id'));

                        return $stats && $stats->total > 0
                            ? number_format((float) $stats->min_price, 2, ',', ' ').' €'
                            : 'Ziadne data';
                    }),

                TextEntry::make('market_max')
                    ->label('Maximum')
                    ->state(function (Get $get): string {
                        $stats = static::stats((int) $get('tenant_product_id'));

                        return $stats && $stats->total > 0
                            ? number_format((float) $stats->max_price, 2, ',', ' ').' €'
                            : 'Ziadne data';
                    }),

                TextEntry::make('market_count')
                    ->label('Pocet zaznamov')
                    ->state(function (Get $get): string {
                        $stats = static::stats((int) $get('tenant_product_id'));

                        return $stats ? (string) $stats->total : '0';
                    }),
            ])->columns(4);
    }

    private static array $statsCache = [];

    private static function stats(int $tenantProductId): ?object
    {
        if (array_key_exists($tenantProductId, self::$statsCache)) {
            return self::$statsCache[$tenantProductId];
        }

        $globalProductId = TenantProduct::find($tenantProductId)?->global_product_id;

        if (! $globalProductId) {
            return self::$statsCache[$tenantProductId] = null;
        }

        $stats = PriceHistory::query()
            ->join('tenant_product_variants as v', 'v.id', '=', 'price_history.tenant_product_variant_id')
            ->join('tenant_products as tp', 'tp.id', '=', 'v.tenant_product_id')
            ->where('tp.global_product_id', $globalProductId)
            ->where('price_history.valid_from', '<=', now())
            ->where(fn ($q) => $q->whereNull('price_history.valid_to')->orWhere('price_history.valid_to', '>=', now()))
            ->selectRaw('AVG(price_history.price) as avg_price, MIN(price_history.price) as min_price, MAX(price_history.price) as max_price, COUNT(*) as total')
            ->first();

        self::$statsCache[$tenantProductId] = $stats;

        return $stats;
    }
}
