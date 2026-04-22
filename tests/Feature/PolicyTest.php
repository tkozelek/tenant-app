<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\Coupons\Pages\ListCoupons as AdminListCoupons;
use App\Filament\Tenant\Resources\Coupons\Pages\ListCoupons as TenantListCoupons;
use App\Models\Coupon;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'platform.access',
            'catalog.manage', 'products.view_any', 'products.create', 'products.update', 'products.delete',
            'products.export', 'products.import',
            'tenant.products.view_any', 'tenant.products.create', 'tenant.products.update', 'tenant.products.delete',
            'coupons.view_any', 'coupons.create', 'coupons.update', 'coupons.delete',
            'coupons.export', 'coupons.import',
            'tenant.coupons.view_any', 'tenant.coupons.create', 'tenant.coupons.update', 'tenant.coupons.delete',
            'tenant.coupons.export',
        ] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }

    private function giveGlobalPermission(User $user, string $permission): void
    {
        setPermissionsTeamId(null);
        $role = Role::firstOrCreate(['name' => 'global-'.$permission, 'guard_name' => 'web', 'tenant_id' => null]);
        $role->givePermissionTo($permission);
        $user->assignRole($role);
        $user->unsetRelation('roles');
        $user->unsetRelation('permissions');
    }

    private function giveTenantPermission(User $user, Tenant $tenant, string $permission): void
    {
        setPermissionsTeamId($tenant->id);
        $role = Role::create(['name' => $permission.'-role', 'guard_name' => 'web', 'tenant_id' => $tenant->id]);
        $role->givePermissionTo($permission);
        $user->assignRole($role);
        $user->unsetRelation('roles');
        $user->unsetRelation('permissions');
    }

    // TenantProduct policy

    public function test_user_with_catalog_manage_can_view_any_products(): void
    {
        $user = User::factory()->create();
        $this->giveGlobalPermission($user, 'catalog.manage');

        $this->assertTrue($user->can('viewAny', TenantProduct::class));
    }

    public function test_user_with_tenant_permission_can_view_product(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $product = TenantProduct::factory()->for($tenant)->create();
        $this->giveTenantPermission($user, $tenant, 'tenant.products.view_any');

        $this->assertTrue($user->can('view', $product));
    }

    public function test_user_without_permission_cannot_view_product(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $product = TenantProduct::factory()->for($tenant)->create();

        $this->assertFalse($user->can('view', $product));
    }

    public function test_user_with_other_tenant_permission_cannot_view_product(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $user = User::factory()->create();
        $productOnB = TenantProduct::factory()->for($tenantB)->create();
        $this->giveTenantPermission($user, $tenantA, 'tenant.products.view_any');

        $this->assertFalse($user->can('view', $productOnB));
    }

    public function test_user_with_catalog_manage_can_update_any_product(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $product = TenantProduct::factory()->for($tenant)->create();
        $this->giveGlobalPermission($user, 'catalog.manage');

        $this->assertTrue($user->can('update', $product));
    }

    public function test_user_with_tenant_update_permission_can_update_own_product(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $product = TenantProduct::factory()->for($tenant)->create();
        $this->giveTenantPermission($user, $tenant, 'tenant.products.update');

        $this->assertTrue($user->can('update', $product));
    }

    public function test_user_cannot_delete_product_without_permission(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $product = TenantProduct::factory()->for($tenant)->create();

        $this->assertFalse($user->can('delete', $product));
    }

    // Coupon policy

    public function test_user_with_coupons_view_any_can_view_coupon(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create(['tenant_id' => $tenant->id]);
        $this->giveGlobalPermission($user, 'coupons.view_any');

        $this->assertTrue($user->can('view', $coupon));
    }

    public function test_user_with_tenant_coupon_permission_can_view_coupon(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create(['tenant_id' => $tenant->id]);
        $this->giveTenantPermission($user, $tenant, 'tenant.coupons.view_any');

        $this->assertTrue($user->can('view', $coupon));
    }

    public function test_user_cannot_view_coupon_from_another_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create(['tenant_id' => $tenantB->id]);
        $this->giveTenantPermission($user, $tenantA, 'tenant.coupons.view_any');

        $this->assertFalse($user->can('view', $coupon));
    }

    public function test_user_with_coupons_update_can_update(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create(['tenant_id' => $tenant->id]);
        $this->giveGlobalPermission($user, 'coupons.update');

        $this->assertTrue($user->can('update', $coupon));
    }

    public function test_user_cannot_delete_coupon_without_permission(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertFalse($user->can('delete', $coupon));
    }

    // Filament resource access

    public function test_admin_user_can_access_coupon_resource(): void
    {
        $user = User::factory()->create();
        $this->giveGlobalPermission($user, 'platform.access');
        $this->giveGlobalPermission($user, 'coupons.view_any');

        Livewire::actingAs($user)
            ->test(AdminListCoupons::class)
            ->assertHasNoErrors();
    }

    public function test_tenant_owner_can_access_coupon_resource(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory()->for($owner, 'owner')->create();
        $this->giveTenantPermission($owner, $tenant, 'tenant.coupons.view_any');

        $this->actingAs($owner);
        Filament::setTenant($tenant);

        Livewire::actingAs($owner)
            ->test(TenantListCoupons::class)
            ->assertHasNoErrors();
    }
}
