<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\GlobalProduct;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TenantRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        setPermissionsTeamId(null);
        (new RoleSeeder)->run();
        (new TenantRoleSeeder)->run();
    }

    private function platformUser(string $roleName): User
    {
        $user = User::factory()->create();
        setPermissionsTeamId(null);
        $user->assignRole(Role::where('name', $roleName)->whereNull('tenant_id')->first());
        $user->unsetRelation('roles');
        $user->unsetRelation('permissions');

        return $user;
    }

    private function tenantUser(Tenant $tenant, array $permissions): User
    {
        $user = User::factory()->create();
        setPermissionsTeamId($tenant->id);
        $user->givePermissionTo($permissions);
        $user->unsetRelation('roles');
        $user->unsetRelation('permissions');

        return $user;
    }

    public function test_catalog_manager_can_manage_catalog_resources(): void
    {
        $user = $this->platformUser('Správca katalógu');

        $this->assertTrue($user->can('viewAny', GlobalProduct::class));
        $this->assertTrue($user->can('viewAny', Category::class));
        $this->assertTrue($user->can('viewAny', Attribute::class));

        $this->assertTrue($user->can('create', GlobalProduct::class));
        $this->assertTrue($user->can('create', Category::class));
        $this->assertTrue($user->can('create', Attribute::class));

        $this->assertTrue($user->can('update', new GlobalProduct));
        $this->assertTrue($user->can('update', new Category));
        $this->assertTrue($user->can('update', new Attribute));

        $this->assertTrue($user->can('delete', new GlobalProduct));
        $this->assertTrue($user->can('delete', new Category));
    }

    public function test_catalog_manager_cannot_access_coupons_users_or_tenants(): void
    {
        $user = $this->platformUser('Správca katalógu');

        $this->assertFalse($user->can('viewAny', Coupon::class));
        $this->assertFalse($user->can('create', Coupon::class));
        $this->assertFalse($user->can('update', new Coupon(['tenant_id' => 1])));

        $this->assertFalse($user->can('create', User::class));
        $this->assertFalse($user->can('create', Tenant::class));
        $this->assertFalse($user->can('update', new Tenant));
    }

    public function test_coupon_manager_can_manage_coupons_and_view_tenants(): void
    {
        $user = $this->platformUser('Správca kupónov');
        $coupon = new Coupon(['tenant_id' => 1]);

        $this->assertTrue($user->can('viewAny', Coupon::class));
        $this->assertTrue($user->can('create', Coupon::class));
        $this->assertTrue($user->can('update', $coupon));
        $this->assertTrue($user->can('delete', $coupon));

        $this->assertTrue($user->can('viewAny', Tenant::class));
    }

    public function test_coupon_manager_cannot_access_catalog_users_or_modify_tenants(): void
    {
        $user = $this->platformUser('Správca kupónov');

        $this->assertFalse($user->can('viewAny', GlobalProduct::class));
        $this->assertFalse($user->can('viewAny', User::class));
        $this->assertFalse($user->can('create', Tenant::class));
        $this->assertFalse($user->can('update', new Tenant));
        $this->assertFalse($user->can('update', new GlobalProduct));
    }

    public function test_user_manager_can_manage_users_and_roles(): void
    {
        $user = $this->platformUser('Správca používateľov');
        $other = User::factory()->create();

        $this->assertTrue($user->can('viewAny', User::class));
        $this->assertTrue($user->can('create', User::class));
        $this->assertTrue($user->can('update', $other));
        $this->assertTrue($user->can('delete', $other));

        $this->assertTrue($user->can('viewAny', Role::class));
        $this->assertTrue($user->can('create', Role::class));
        $this->assertTrue($user->can('update', new Role));
        $this->assertTrue($user->can('delete', new Role));
    }

    public function test_user_manager_cannot_access_catalog_coupons_or_tenants(): void
    {
        $user = $this->platformUser('Správca používateľov');

        $this->assertFalse($user->can('viewAny', GlobalProduct::class));
        $this->assertFalse($user->can('viewAny', Coupon::class));
        $this->assertFalse($user->can('create', Tenant::class));
        $this->assertFalse($user->can('update', new GlobalProduct));
        $this->assertFalse($user->can('update', new Coupon(['tenant_id' => 1])));
    }

    public function test_tenant_manager_can_manage_tenants_and_view_users(): void
    {
        $user = $this->platformUser('Správca prevádzok');

        $this->assertTrue($user->can('viewAny', Tenant::class));
        $this->assertTrue($user->can('create', Tenant::class));
        $this->assertTrue($user->can('update', new Tenant));

        $this->assertTrue($user->can('viewAny', User::class));
    }

    public function test_tenant_manager_cannot_manage_catalog_coupons_or_create_users(): void
    {
        $user = $this->platformUser('Správca prevádzok');
        $other = User::factory()->create();

        $this->assertFalse($user->can('viewAny', GlobalProduct::class));
        $this->assertFalse($user->can('viewAny', Coupon::class));
        $this->assertFalse($user->can('create', User::class));
        $this->assertFalse($user->can('delete', $other));
        $this->assertFalse($user->can('update', new GlobalProduct));
    }

    public function test_analyst_has_read_only_view_access(): void
    {
        $user = $this->platformUser('Analytik');

        $this->assertTrue($user->can('viewAny', GlobalProduct::class));
        $this->assertTrue($user->can('viewAny', Tenant::class));
    }

    public function test_analyst_cannot_create_update_or_delete_any_resource(): void
    {
        $user = $this->platformUser('Analytik');

        $this->assertFalse($user->can('create', GlobalProduct::class));
        $this->assertFalse($user->can('update', new GlobalProduct));
        $this->assertFalse($user->can('delete', new GlobalProduct));

        $this->assertFalse($user->can('viewAny', Coupon::class));
        $this->assertFalse($user->can('create', User::class));
        $this->assertFalse($user->can('create', Tenant::class));
    }

    public function test_product_manager_can_manage_tenant_products_and_coupons(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory()->for($owner, 'owner')->create();
        $user = $this->tenantUser($tenant, [
            'tenant.products.view_any', 'tenant.products.create', 'tenant.products.update', 'tenant.products.delete',
            'tenant.coupons.view_any', 'tenant.coupons.create', 'tenant.coupons.update', 'tenant.coupons.delete',
        ]);

        $product = TenantProduct::factory()->make(['tenant_id' => $tenant->id]);
        $coupon = Coupon::factory()->make(['tenant_id' => $tenant->id]);

        $this->assertTrue($user->can('view', $product));
        $this->assertTrue($user->can('update', $product));
        $this->assertTrue($user->can('delete', $product));

        $this->assertTrue($user->can('view', $coupon));
        $this->assertTrue($user->can('update', $coupon));
        $this->assertTrue($user->can('delete', $coupon));
    }

    public function test_warehouse_staff_can_view_products_but_cannot_modify_or_access_coupons(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory()->for($owner, 'owner')->create();
        $user = $this->tenantUser($tenant, [
            'tenant.products.view_any',
            'tenant.variants.view_any',
            'tenant.variants.update',
        ]);

        $product = TenantProduct::factory()->make(['tenant_id' => $tenant->id]);
        $coupon = Coupon::factory()->make(['tenant_id' => $tenant->id]);

        $this->assertTrue($user->can('view', $product));

        $this->assertFalse($user->can('update', $product));
        $this->assertFalse($user->can('delete', $product));

        $this->assertFalse($user->can('view', $coupon));
        $this->assertFalse($user->can('update', $coupon));
    }
}
