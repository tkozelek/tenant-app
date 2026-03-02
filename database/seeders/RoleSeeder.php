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
        ];

        $tenantPermissions = [
            'tenants.view_any',
            'tenants.create',
            'tenants.update',
            'tenants.delete',
        ];

        $rolePermissions = [
            'roles.view_any',
            'roles.create',
            'roles.update',
            'roles.delete',
        ];

        $userPermissions = [
            'users.view_any',
            'users.create',
            'users.update',
            'users.delete',
        ];

        $productPermissions = [
            'products.view_any',
            'products.create',
            'products.update',
            'products.delete',
        ];

        $categoryPermissions = [
            'categories.view_any',
            'categories.create',
            'categories.update',
            'categories.delete',
        ];

        $attributePermissions = [
            'attributes.view_any',
            'attributes.create',
            'attributes.update',
            'attributes.delete',
        ];

        $storePermissions = [
            'store.access',
            'store.settings',
            'store.users.manage',
            'store.products.manage',
            'store.orders.view',
            'store.orders.manage',
            'store.reports.view',
        ];

        $allPermissions = array_merge(
            $platformPermissions,
            $tenantPermissions,
            $rolePermissions,
            $userPermissions,
            $categoryPermissions,
            $attributePermissions,
            $productPermissions,
            $storePermissions,
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', config('permission.column_names.team_foreign_key') => null]);
        $superAdmin->syncPermissions(Permission::all());

        $platformAdmin = Role::firstOrCreate(['name' => 'Platform Admin', config('permission.column_names.team_foreign_key') => null]);
        $platformAdmin->syncPermissions(array_merge(
            $platformPermissions,
            $tenantPermissions,
            ['users.view_any'],
            $rolePermissions,
            $categoryPermissions,
            $attributePermissions,
            $productPermissions,
        ));

        $tenantOwner = Role::firstOrCreate(['name' => 'Tenant Owner', config('permission.column_names.team_foreign_key') => null]);
        $tenantOwner->syncPermissions($storePermissions);

        $shopManager = Role::firstOrCreate(['name' => 'Shop Manager', config('permission.column_names.team_foreign_key') => null]);
        $shopManager->syncPermissions([
            'store.access',
            'store.products.manage',
            'store.orders.view',
            'store.orders.manage',
            'store.reports.view',
        ]);

        $salesStaff = Role::firstOrCreate(['name' => 'Sales Staff', config('permission.column_names.team_foreign_key') => null]);
        $salesStaff->syncPermissions([
            'store.access',
            'store.orders.view',
            'store.orders.manage',
            'store.products.manage',
        ]);

        $warehouseStaff = Role::firstOrCreate(['name' => 'Warehouse Staff', config('permission.column_names.team_foreign_key') => null]);
        $warehouseStaff->syncPermissions([
            'store.access',
            'store.orders.view',
            'store.orders.manage',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'Viewer', config('permission.column_names.team_foreign_key') => null]);
        $viewer->syncPermissions([
            'store.access',
            'store.orders.view',
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
        if (! $user->hasRole($platformAdmin)) {
            $user->assignRole($platformAdmin);
        }
    }
}
