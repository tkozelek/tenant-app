<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\User;

class TenantProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('tenant.access') || $user->hasPermissionTo('products.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TenantProduct $tenantProduct): bool
    {
        return $user->hasPermissionToOnTenant('tenant.access', $tenantProduct->tenant_id) || $user->hasPermissionTo('products.view_any');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->hasPermissionTo('products.create') || $user->hasPermissionTo('tenant.products.manage');
    }

    public function createForTenant(User $user, Tenant $tenant): bool
    {
        return  $user->hasPermissionTo('products.create') || $user->hasPermissionToOnTenant('tenant.products.manage', $tenant->id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TenantProduct $tenantProduct): bool
    {
        return $user->hasPermissionTo('products.update') || $user->hasPermissionToOnTenant('tenant.products.manage', $tenantProduct->tenant_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TenantProduct $tenantProduct): bool
    {
        return $user->hasPermissionTo('products.delete') || $user->hasPermissionToOnTenant('tenant.products.manage', $tenantProduct->tenant_id);
    }

    public function exportAny(User $user): bool
    {
        return $user->hasPermissionTo('products.export');
    }

    public function importAny(User $user): bool
    {
        return $user->hasPermissionTo('products.import');
    }
}
