<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TenantProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // This is tricky for "viewAny" because it depends on "which tenant" we are viewing.
        // Usually, controllers filter by tenant first.
        // We can check if the user has this permission on *any* tenant or globally.
        return $user->hasPermissionTo('store.access');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TenantProduct $tenantProduct): bool
    {
        // Check if user has permission specifically for this product's tenant
        return $user->hasPermissionToOnTenant('store.access', $tenantProduct->tenant_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // This usually requires context of *which* tenant they are creating for.
        // If you pass the tenant instance to the policy (e.g., $user->can('create', [TenantProduct::class, $tenant]))
        // But standard resource policies don't always pass the parent.
        // Assuming the controller checks the tenant context or we check if they have it on the "current" tenant if set.

        // For now, we return true if they have it generally, but the controller must enforce the specific tenant check
        // OR we rely on the fact that they must be logged in / scoped to a tenant.
        return $user->hasPermissionTo('store.products.manage');
    }

    // Better create check if we can pass the tenant
    public function createForTenant(User $user, Tenant $tenant): bool
    {
        return $user->hasPermissionToOnTenant('store.products.manage', $tenant->id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TenantProduct $tenantProduct): bool
    {
        return $user->hasPermissionToOnTenant('store.products.manage', $tenantProduct->tenant_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TenantProduct $tenantProduct): bool
    {
        return $user->hasPermissionToOnTenant('store.products.manage', $tenantProduct->tenant_id);
    }
}
