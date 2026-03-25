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

        $tenantGeneralPermissions = [
            'tenant.access',
            'tenant.settings',
            'tenant.update',
            'tenant.users.manage',
            'tenant.reports.view',
            'tenant.activity_log.view',
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
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 1. Super Admin — unrestricted access to everything
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

        // 2. Platform Admin — manages all platform resources except creating/deleting users
        $platformAdmin = Role::firstOrCreate(
            [
                'name' => 'Administrátor platformy',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Spravuje celú platformu, jednotlivé prevádzky (nájomcov), používateľov a globálny katalóg produktov. Nemá prístup k použivateľom.',
            ]
        );
        $platformAdmin->syncPermissions(array_merge(
            $platformPermissions,
            $tenantPermissions,
            $couponsPermissions,
            ['users.view_any'],
            $rolePermissions,
            $categoryPermissions,
            $attributePermissions,
            $productPermissions,
        ));

        $tenantOwner = Role::firstOrCreate(
        // 3. Tenant Manager — manages tenant businesses and their operations
            [
                'name' => 'Majiteľ prevádzky',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Hlavný správca konkrétnej prevádzky s plným prístupom k jej nastaveniam, používateľom a skladovým zásobám.',
            ]
        );
        $tenantOwner->syncPermissions($tenantPermissions);

        $shopManager = Role::firstOrCreate(
        // 4. Catalog Manager — manages the global product catalog
            [
                'name' => 'Manažér prevádzky',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Zabezpečuje organizáciu prevádzky, kompletne spravuje produkty, kategórie a prezerá štatistiky skladu.',
            ]
        );
        $shopManager->syncPermissions(array_merge(
            $tenantGeneralPermissions,
            $tenantProductPermissions,
            $tenantVariantPermissions,
            $tenantRequestPermissions,
            $tenantCouponPermissions,
        ));

        $productStaff = Role::firstOrCreate(
        // 5. User & Role Manager — manages user accounts and role assignments
            [
                'name' => 'Správca produktov',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Zodpovedá za evidenciu, pridávanie a aktualizáciu informácií o produktoch v systéme.',
            ]
        );
        $productStaff->syncPermissions(array_merge(
            ['tenant.access', 'tenant.reports.view'],
            $tenantProductPermissions,
            $tenantVariantPermissions,
            $tenantRequestPermissions,
        ));

        $warehouseStaff = Role::firstOrCreate(
        // 6. Coupon Manager — manages platform-wide coupons
            [
                'name' => 'Pracovník skladu',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Má na starosti fyzickú kontrolu a dennú aktualizáciu skladových zásob produktov.',
            ]
        );
        $warehouseStaff->syncPermissions([
            'tenant.access',
            'tenant.products.view_any',
            'tenant.variants.view_any',
            'tenant.variants.update',
        ]);
        // 7. Report Analyst — read-only access to platform reports

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
