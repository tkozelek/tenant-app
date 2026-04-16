<?php

namespace Database\Seeders;

use App\Enums\PermissionScope;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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

        $xmlFeedPermissions = [
            'xml_feeds.view_any',
            'xml_feeds.create',
            'xml_feeds.update',
            'xml_feeds.delete',
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

        $appPermissions = array_merge(
            $platformPermissions,
            $tenantsPermissions,
            $rolePermissions,
            $userPermissions,
            $couponsPermissions,
            $categoryPermissions,
            $attributePermissions,
            $productPermissions,
            $reportPermissions,
            $apiTokenPermissions,
            $bundlesPermissions,
            $xmlFeedPermissions,
        );

        foreach ($appPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission], ['scope' => PermissionScope::App]);
        }

        foreach ($tenantGeneralPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission], ['scope' => PermissionScope::Tenant]);
        }

        $superAdmin = Role::firstOrCreate(
            [
                'name' => 'Super Administrátor',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Má absolútny prístup ku všetkým funkciám a nastaveniam celého systému.',
                'scope' => PermissionScope::App,
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
                'scope' => PermissionScope::App,
            ]
        );
        $platformAdmin->syncPermissions(array_merge(
            $platformPermissions,
            $tenantsPermissions,
            $couponsPermissions,
            $reportPermissions,
            $apiTokenPermissions,
            ['users.view_any', 'users.update', 'users.export'],
            $rolePermissions,
            $categoryPermissions,
            $attributePermissions,
            $productPermissions,
            $bundlesPermissions,
            $xmlFeedPermissions,
        ));

        $tenantManager = Role::firstOrCreate(
            [
                'name' => 'Správca prevádzok',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje prevádzky a ich prevádzkové dáta. Nemá prístup ku globálnemu katalógu ani správe používateľov.',
                'scope' => PermissionScope::App,
            ]
        );
        $tenantManager->syncPermissions(array_merge(
            ['platform.access'],
            $tenantsPermissions,
            ['users.view_any'],
            $reportPermissions,
            $xmlFeedPermissions,
        ));

        $catalogManager = Role::firstOrCreate(
            [
                'name' => 'Správca katalógu',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje globálny katalóg produktov, kategórie, atribúty a požiadavky na nové produkty.',
                'scope' => PermissionScope::App,
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
                'scope' => PermissionScope::App,
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
                'scope' => PermissionScope::App,
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
                'scope' => PermissionScope::App,
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
                'password' => Hash::make('password'),
            ]
        );

        setPermissionsTeamId(null);

        $user->assignRole($superAdmin);
    }
}
