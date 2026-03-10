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

        $tenantPermissions = [
            'tenant.owner',
            'tenant.access',
            'tenant.settings',
            'tenant.update',
            'tenant.users.manage',
            'tenant.products.manage',
            'tenant.reports.view',
        ];

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
            [
                'name' => 'Manažér prevádzky',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Zabezpečuje organizáciu prevádzky, kompletne spravuje produkty, kategórie a prezerá štatistiky skladu.',
            ]
        );
        $shopManager->syncPermissions([
            'tenant.access',
            'tenant.products.manage',
            'tenant.reports.view',
        ]);

        $productStaff = Role::firstOrCreate(
            [
                'name' => 'Správca produktov',
                config('permission.column_names.team_foreign_key') => null,
            ],
            [
                'description' => 'Zodpovedá za evidenciu, pridávanie a aktualizáciu informácií o produktoch v systéme.',
            ]
        );
        $productStaff->syncPermissions([
            'tenant.access',
            'tenant.products.manage',
        ]);

        $warehouseStaff = Role::firstOrCreate(
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
            'tenant.products.manage',
        ]);

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
