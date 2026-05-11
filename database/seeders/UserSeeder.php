<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
            ]
        );

        setPermissionsTeamId(null);
        $superAdmin = Role::where('name', 'Super Administrátor')->whereNull('tenant_id')->first();
        if ($superAdmin) {
            $admin->assignRole($superAdmin);
        }

        $testUser = User::firstOrCreate(
            ['email' => 'test@test.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'password' => Hash::make('password'),
            ]
        );

        $ownerRole = Role::where('name', 'Majiteľ prevádzky')->whereNull('tenant_id')->first();

        if ($ownerRole) {
            $tenants = Tenant::take(5)->get();

            foreach ($tenants as $tenant) {
                setPermissionsTeamId($tenant->id);
                $testUser->unsetRelation('roles');
                $testUser->assignRole($ownerRole);
            }

            setPermissionsTeamId(null);
        }
    }
}
