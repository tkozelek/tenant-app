<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;

    private function adminPanel(): Panel
    {
        return app('filament')->getPanel('admin');
    }

    private function tenantPanel(): Panel
    {
        return app('filament')->getPanel('tenant');
    }

    private function giveGlobalPermission(User $user, string $permission): void
    {
        $perm = Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        setPermissionsTeamId(null);
        $role = Role::firstOrCreate(['name' => 'global-'.$permission, 'guard_name' => 'web', 'tenant_id' => null]);
        $role->givePermissionTo($perm);
        $user->assignRole($role);
        $user->unsetRelation('roles');
        $user->unsetRelation('permissions');
    }

    public function test_user_with_platform_access_permission_can_access_admin_panel(): void
    {
        $user = User::factory()->create();
        $this->giveGlobalPermission($user, 'platform.access');

        setPermissionsTeamId(null);
        $user->unsetRelation('roles');
        $user->unsetRelation('permissions');

        $this->assertTrue($user->canAccessPanel($this->adminPanel()));
    }

    public function test_user_without_platform_access_cannot_access_admin_panel(): void
    {
        Permission::firstOrCreate(['name' => 'platform.access', 'guard_name' => 'web']);
        $user = User::factory()->create();

        $this->assertFalse($user->canAccessPanel($this->adminPanel()));
    }

    public function test_tenant_owner_can_access_tenant_panel(): void
    {
        $tenant = Tenant::factory()->create();
        $owner = User::find($tenant->owner_id);

        $this->assertTrue($owner->canAccessPanel($this->tenantPanel()));
    }

    public function test_user_with_no_tenants_cannot_access_tenant_panel(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->canAccessPanel($this->tenantPanel()));
    }

    public function test_tenant_member_with_role_can_access_tenant_panel(): void
    {
        $tenant = Tenant::factory()->create();
        $member = User::factory()->create();

        setPermissionsTeamId($tenant->id);
        $role = Role::create(['name' => 'staff', 'guard_name' => 'web', 'tenant_id' => $tenant->id]);
        $member->assignRole($role);

        $this->assertTrue($member->canAccessPanel($this->tenantPanel()));
    }

    public function test_owner_can_access_their_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $owner = User::find($tenant->owner_id);

        $this->assertTrue($owner->canAccessTenant($tenant));
    }

    public function test_member_can_access_tenant_they_belong_to(): void
    {
        $tenant = Tenant::factory()->create();
        $member = User::factory()->create();

        setPermissionsTeamId($tenant->id);
        $role = Role::create(['name' => 'staff', 'guard_name' => 'web', 'tenant_id' => $tenant->id]);
        $member->assignRole($role);

        $this->assertTrue($member->canAccessTenant($tenant));
    }

    public function test_unrelated_user_cannot_access_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $stranger = User::factory()->create();

        $this->assertFalse($stranger->canAccessTenant($tenant));
    }

    public function test_owner_of_one_tenant_cannot_access_another_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $ownerA = User::find($tenantA->owner_id);

        $this->assertFalse($ownerA->canAccessTenant($tenantB));
    }
}
