<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $platformPermissions = [
            'platform.access',
            'catalog.manage',
        ];

        $tenantsPermissions = [
            'tenants.view_any',
            'tenants.create',
            'tenants.update',
            'tenants.delete',
            'tenants.export',
            'tenants.import',
        ];

        $rolePermissions = [
            'roles.view_any',
            'roles.create',
            'roles.update',
            'roles.delete',
            'roles.export',
            'roles.import',
        ];

        $userPermissions = [
            'users.view_any',
            'users.create',
            'users.update',
            'users.delete',
            'users.export',
            'users.import',
        ];

        $productPermissions = [
            'products.view_any',
            'products.create',
            'products.update',
            'products.delete',
            'products.export',
            'products.import',
        ];

        $categoryPermissions = [
            'categories.view_any',
            'categories.create',
            'categories.update',
            'categories.delete',
            'categories.export',
            'categories.import',
        ];

        $attributePermissions = [
            'attributes.view_any',
            'attributes.create',
            'attributes.update',
            'attributes.delete',
            'attributes.export',
            'attributes.import',
        ];

        $couponsPermissions = [
            'coupons.view_any',
            'coupons.create',
            'coupons.update',
            'coupons.delete',
            'coupons.export',
            'coupons.import',
        ];

        $bundlesPermissions = [
            'bundles.view_any',
            'bundles.create',
            'bundles.update',
            'bundles.delete',
            'bundles.export',
            'bundles.import',
        ];

        $reportPermissions = [
            'reports.expiring_prices',
            'reports.price_movement',
            'reports.coupon_performance',
        ];

        $apiTokenPermissions = [
            'api_tokens.view_any',
            'api_tokens.create',
            'api_tokens.delete',
        ];

        $tenantGeneralPermissions = [
            'tenant.access',
            'tenant.settings',
            'tenant.update',
            'tenant.users.manage',
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

        $tenantPermissions = array_merge(
            $tenantGeneralPermissions,
            $tenantReportPermissions,
            $tenantProductPermissions,
            $tenantVariantPermissions,
            $tenantRequestPermissions,
            $tenantCouponPermissions,
        );

        $allPermissions = array_merge(
            $platformPermissions,
            $tenantsPermissions,
            $tenantPermissions,
            $rolePermissions,
            $userPermissions,
            $couponsPermissions,
            $categoryPermissions,
            $attributePermissions,
            $productPermissions,
            $reportPermissions,
            $apiTokenPermissions,
            $bundlesPermissions,
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(
            [
                'name' => 'Super Administrátor',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Má absolútny prístup ku všetkým funkciám a nastaveniam celého systému.',
            ]
        );
        $superAdmin->syncPermissions(Permission::all());

        $platformAdmin = Role::firstOrCreate(
            [
                'name' => 'Administrátor platformy',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje celú platformu, prevádzky a globálny katalóg. Nemá právo vytvárať ani mazať používateľov.',
            ]
        );
        $platformAdmin->syncPermissions(array_merge(
            $platformPermissions,
            $tenantsPermissions,
            $tenantPermissions,
            $couponsPermissions,
            $reportPermissions,
            $apiTokenPermissions,
            ['users.view_any', 'users.update', 'users.export'],
            $rolePermissions,
            $categoryPermissions,
            $attributePermissions,
            $productPermissions,
            $bundlesPermissions,
        ));

        $tenantManager = Role::firstOrCreate(
            [
                'name' => 'Správca prevádzok',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje prevádzky a ich prevádzkové dáta. Nemá prístup ku globálnemu katalógu ani správe používateľov.',
            ]
        );
        $tenantManager->syncPermissions(array_merge(
            ['platform.access'],
            $tenantsPermissions,
            $tenantPermissions,
            ['users.view_any'],
            $reportPermissions,
        ));

        $catalogManager = Role::firstOrCreate(
            [
                'name' => 'Správca katalógu',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje globálny katalóg produktov, kategórie, atribúty a požiadavky na nové produkty.',
            ]
        );
        $catalogManager->syncPermissions(array_merge(
            ['platform.access', 'catalog.manage'],
            $productPermissions,
            $categoryPermissions,
            $attributePermissions,
            $bundlesPermissions,
        ));

        $userManager = Role::firstOrCreate(
            [
                'name' => 'Správca používateľov',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje používateľské účty a ich roly v systéme. Nemá prístup k prevádzkovým ani katalógovým dátam.',
            ]
        );
        $userManager->syncPermissions(array_merge(
            ['platform.access'],
            $userPermissions,
            $rolePermissions,
        ));

        $couponManager = Role::firstOrCreate(
            [
                'name' => 'Správca kupónov',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje zľavové kupóny naprieč celou platformou.',
            ]
        );
        $couponManager->syncPermissions(array_merge(
            ['platform.access', 'tenants.view_any'],
            $couponsPermissions,
        ));

        $reportAnalyst = Role::firstOrCreate(
            [
                'name' => 'Analytik',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Má prístup iba k reportom a štatistikám platformy. Nemôže upravovať žiadne dáta.',
            ]
        );
        $reportAnalyst->syncPermissions(array_merge(
            ['platform.access', 'tenants.view_any', 'products.view_any'],
            $reportPermissions,
        ));

        $user = User::firstOrCreate(
            ['email' => 'tommyside@centrum.sk'],
            [
                'first_name' => 'Tomáš',
                'last_name' => 'Kozelek',
                'password' => '$2y$12$Nk/uzpXJlK/lj35hIAhmTOHpuIv/A/.YvrElf4i0E0rYibpBkV2NC',
            ]
        );

        setPermissionsTeamId(null);

        $user->assignRole($superAdmin);
    }
}
