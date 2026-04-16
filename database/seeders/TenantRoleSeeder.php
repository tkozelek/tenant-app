<?php

namespace Database\Seeders;

use App\Enums\PermissionScope;
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
            'tenant.reports.stale_products',
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

        $tenantXmlFeedPermissions = [
            'tenant.xml_feeds.view_any',
            'tenant.xml_feeds.create',
            'tenant.xml_feeds.update',
            'tenant.xml_feeds.delete',
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
            $tenantXmlFeedPermissions,
        );

        foreach ($allTenantPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission], ['scope' => PermissionScope::Tenant]);
        }

        $tenantOwner = Role::firstOrCreate(
            [
                'name' => 'Majiteľ prevádzky',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Ma plny pristup ku vsetkemu v prevadzke, spravuje nastavenia, userov aj produkty.',
                'scope' => PermissionScope::Tenant,
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
                'scope' => PermissionScope::Tenant,
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
            $tenantXmlFeedPermissions,
        ));

        $productManager = Role::firstOrCreate(
            [
                'name' => 'Správca produktov',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Stara sa o produkty, varianty, kupony a balicky prevadzky.',
                'scope' => PermissionScope::Tenant,
            ]
        );
        $productManager->syncPermissions(array_merge(
            ['tenant.access'],
            $tenantReportPermissions,
            $tenantProductPermissions,
            $tenantVariantPermissions,
            $tenantRequestPermissions,
            $tenantCouponPermissions,
            $tenantBundlePermissions,
            ['tenant.xml_feeds.view_any'],
        ));

        $warehouseStaff = Role::firstOrCreate(
            [
                'name' => 'Pracovník skladu',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Kontroluje sklad a aktualizuje mnozstva produktov.',
                'scope' => PermissionScope::Tenant,
            ]
        );
        $warehouseStaff->syncPermissions([
            'tenant.access',
            'tenant.products.view_any',
            'tenant.variants.view_any',
            'tenant.variants.update',
        ]);

        $apiTokenManager = Role::firstOrCreate(
            [
                'name' => 'Správca integrácií',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje API tokeny a XML feedy prevadzky pre externu integraci.',
                'scope' => PermissionScope::Tenant,
            ]
        );
        $apiTokenManager->syncPermissions(array_merge(
            [
                'tenant.access',
                'tenant.api_tokens.manage',
                'tenant.view_api_docs',
            ],
            $tenantXmlFeedPermissions,
        ));

        $tenantAnalyst = Role::firstOrCreate(
            [
                'name' => 'Analytik prevádzky',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Pozera iba reporty a statistiky, nic neupravuje.',
                'scope' => PermissionScope::Tenant,
            ]
        );
        $tenantAnalyst->syncPermissions(array_merge(
            ['tenant.access', 'tenant.products.view_any', 'tenant.variants.view_any', 'tenant.bundles.view_any', 'tenant.coupons.view_any'],
            $tenantReportPermissions,
        ));

    }
}
