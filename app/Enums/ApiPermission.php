<?php

namespace App\Enums;

enum ApiPermission: string
{
    case ProductsRead = 'api.products.read';
    case ProductsWrite = 'api.products.write';
    case StockRead = 'api.stock.read';
    case StockWrite = 'api.stock.write';
    case PricesRead = 'api.prices.read';
    case PricesWrite = 'api.prices.write';
    case CouponsRead = 'api.coupons.read';
    case CouponsWrite = 'api.coupons.write';
    case ReportsRead = 'api.reports.read';
    case BundlesRead = 'api.bundles.read';

    public function label(): string
    {
        return match ($this) {
            self::ProductsRead => 'Produkty - Citanie',
            self::ProductsWrite => 'Produkty - Zapis',
            self::StockRead => 'Sklad - Citanie',
            self::StockWrite => 'Sklad - Zapis',
            self::PricesRead => 'Ceny - Citanie',
            self::PricesWrite => 'Ceny - Zapis',
            self::CouponsRead => 'Kupony - Citanie',
            self::CouponsWrite => 'Kupony - Zapis',
            self::ReportsRead => 'Reporty - Citanie',
            self::BundlesRead => 'Bundles - Citanie',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}
