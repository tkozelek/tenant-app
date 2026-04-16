<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TenantRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TenantPanelLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TenantRoleSeeder::class);
        $this->seed(RoleSeeder::class);
    }

    public function test_unauthenticated_user_is_redirected_from_tenant_panel(): void
    {
        $tenant = Tenant::factory()->create();

        $this->get(route('filament.tenant.pages.dashboard', ['tenant' => $tenant]))
            ->assertRedirect();
    }

    public function test_user_without_tenant_role_cannot_access_tenant_panel(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('filament.tenant.pages.dashboard', ['tenant' => $tenant]))
            ->assertForbidden();
    }

    public function test_tenant_owner_can_access_tenant_panel(): void
    {
        $tenant = Tenant::factory()->create();
        $owner = User::find($tenant->owner_id);

        $this->actingAs($owner)
            ->get(route('filament.tenant.pages.dashboard', ['tenant' => $tenant]))
            ->assertSuccessful();
    }

    public function test_tenant_member_with_role_can_access_tenant_panel(): void
    {
        $tenant = Tenant::factory()->create();
        $member = User::factory()->create();

        setPermissionsTeamId($tenant->id);
        $role = Role::where('name', 'Správca produktov')->first();
        $member->assignRole($role);

        $this->actingAs($member)
            ->get(route('filament.tenant.pages.dashboard', ['tenant' => $tenant]))
            ->assertSuccessful();
    }
}
