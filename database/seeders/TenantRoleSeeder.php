<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TenantRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $tenantGeneralPermissions = [
            'tenant.access',
            'tenant.settings',
            'tenant.update',
            'tenant.users.manage',
            'tenant.users.view',
            'tenant.roles.manage',
            'tenant.api_tokens.manage',
            'tenant.activity_log.view',
            'tenant.view_api_docs',
        ];

        $tenantReportPermissions = [
            'tenant.reports.expiring_prices',
            'tenant.reports.price_movement',
            'tenant.reports.stock_health',
            'tenant.reports.coupon_performance',
        ];

        $tenantProductPermissions = [
            'tenant.products.view_any',
            'tenant.products.create',
            'tenant.products.update',
            'tenant.products.delete',
        ];

        $tenantVariantPermissions = [
            'tenant.variants.view_any',
            'tenant.variants.create',
            'tenant.variants.update',
            'tenant.variants.delete',
        ];

        $tenantRequestPermissions = [
            'tenant.requests.view_any',
            'tenant.requests.create',
            'tenant.requests.update',
            'tenant.requests.delete',
        ];

        $tenantCouponPermissions = [
            'tenant.coupons.view_any',
            'tenant.coupons.create',
            'tenant.coupons.update',
            'tenant.coupons.delete',
            'tenant.coupons.export',
        ];

        $tenantBundlePermissions = [
            'tenant.bundles.view_any',
            'tenant.bundles.create',
            'tenant.bundles.update',
            'tenant.bundles.delete',
        ];

        $tenantApiTokenPermissions = [
            'tenant.api_tokens.manage',
        ];

        $allTenantPermissions = array_merge(
            $tenantGeneralPermissions,
            $tenantReportPermissions,
            $tenantProductPermissions,
            $tenantVariantPermissions,
            $tenantRequestPermissions,
            $tenantCouponPermissions,
            $tenantBundlePermissions,
            $tenantApiTokenPermissions,
        );

        foreach ($allTenantPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $tenantOwner = Role::firstOrCreate(
            [
                'name' => 'Majiteľ prevádzky',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Ma plny pristup ku vsetkemu v prevadzke, spravuje nastavenia, userov aj produkty.',
            ]
        );
        $tenantOwner->syncPermissions($allTenantPermissions);

        $shopManager = Role::firstOrCreate(
            [
                'name' => 'Manažér prevádzky',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Riadi prevadzku, spravuje produkty a pozera statistiky, ale nerobi userov ani settings.',
            ]
        );
        $shopManager->syncPermissions(array_merge(
            ['tenant.access', 'tenant.activity_log.view', 'tenant.view_api_docs'],
            $tenantReportPermissions,
            $tenantProductPermissions,
            $tenantVariantPermissions,
            $tenantRequestPermissions,
            $tenantCouponPermissions,
            $tenantBundlePermissions,
        ));

        $productManager = Role::firstOrCreate(
            [
                'name' => 'Správca produktov',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Stara sa o produkty, pridava a upravuje ich a ich info.',
            ]
        );
        $productManager->syncPermissions(array_merge(
            ['tenant.access'],
            $tenantReportPermissions,
            $tenantProductPermissions,
            $tenantVariantPermissions,
            $tenantRequestPermissions,
            $tenantBundlePermissions,
        ));

        $variantManager = Role::firstOrCreate(
            [
                'name' => 'Správca variantov',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje varianty produktov, ceny a sklad, ale nemeni produkty.',
            ]
        );
        $variantManager->syncPermissions(array_merge(
            ['tenant.access', 'tenant.products.view_any', 'tenant.bundles.view_any'],
            $tenantVariantPermissions,
        ));

        $warehouseStaff = Role::firstOrCreate(
            [
                'name' => 'Pracovník skladu',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Kontroluje sklad a aktualizuje mnozstva produktov.',
            ]
        );
        $warehouseStaff->syncPermissions([
            'tenant.access',
            'tenant.products.view_any',
            'tenant.variants.view_any',
            'tenant.variants.update',
        ]);

        $tenantCouponManager = Role::firstOrCreate(
            [
                'name' => 'Správca kupónov prevádzky',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje kupony v prevadzke.',
            ]
        );
        $tenantCouponManager->syncPermissions(array_merge(
            ['tenant.access', 'tenant.bundles.view_any'],
            $tenantCouponPermissions,
        ));

        $apiTokenManager = Role::firstOrCreate(
            [
                'name' => 'Správca API prístupu',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje API kluce a pristupy pre externy system.',
            ]
        );
        $apiTokenManager->syncPermissions([
            'tenant.access',
            'tenant.api_tokens.manage',
            'tenant.view_api_docs',
        ]);

        $tenantAnalyst = Role::firstOrCreate(
            [
                'name' => 'Analytik prevádzky',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Pozera iba reporty a statistiky, nic neupravuje.',
            ]
        );
        $tenantAnalyst->syncPermissions(array_merge(
            ['tenant.access', 'tenant.products.view_any', 'tenant.variants.view_any', 'tenant.bundles.view_any'],
            $tenantReportPermissions,
        ));

        $requestManager = Role::firstOrCreate(
            [
                'name' => 'Správca požiadaviek',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje poziadavky na nove produkty.',
            ]
        );
        $requestManager->syncPermissions(array_merge(
            ['tenant.access', 'tenant.products.view_any'],
            $tenantRequestPermissions,
        ));
    }
}
