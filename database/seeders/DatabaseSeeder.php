<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Default User
        $user = User::firstOrCreate(
            ['email' => 'tommyside@centrum.sk'],
            [
                'first_name' => 'Tomáš',
                'last_name' => 'Kozelek',
                'password' => '$2y$12$Nk/uzpXJlK/lj35hIAhmTOHpuIv/A/.YvrElf4i0E0rYibpBkV2NC',
            ]
        );

        $permissions = [
            'platform.access',

            'tenants.view_any',
            'tenants.create',
            'tenants.update',
            'tenants.delete',

            'roles.view_any',
            'roles.create',
            'roles.update',
            'roles.delete',

            'users.view_any',
            'users.create',
            'users.update',
            'users.delete',

            'catalog.manage',

            'store.access',
            'store.settings',
            'store.users.manage',
            'store.products.manage',
            'store.orders.view',
            'store.orders.manage',
            'store.reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', config('permission.column_names.team_foreign_key') => null]);
        $superAdmin->syncPermissions(Permission::all());

        $platformAdmin = Role::firstOrCreate(['name' => 'Platform Admin', config('permission.column_names.team_foreign_key') => null]);
        $platformAdmin->syncPermissions([
            'platform.access',
            'tenants.view_any',
            'tenants.create',
            'tenants.update',
            'tenants.delete',

            'users.view_any',

            'roles.view_any',
            'roles.create',
            'roles.update',
            'roles.delete',

            'catalog.manage',
        ]);

        $tenantOwner = Role::firstOrCreate(['name' => 'Tenant Owner', config('permission.column_names.team_foreign_key') => null]);
        $tenantOwner->syncPermissions([
            'store.access',
            'store.settings',
            'store.users.manage',
            'store.products.manage',
            'store.orders.view',
            'store.orders.manage',
            'store.reports.view',
        ]);

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
            'store.products.manage'
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

        setPermissionsTeamId(null);
        $user->assignRole($platformAdmin);
    }
}
