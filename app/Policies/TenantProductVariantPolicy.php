<?php

namespace App\Policies;

use App\Models\TenantProductVariant;
use App\Models\User;
class TenantProductVariantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('catalog.manage') || $user->hasPermissionTo('store.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TenantProductVariant $variant): bool
    {
        return $user->hasPermissionTo('catalog.manage') ||  $user->hasPermissionToOnTenant('store.manage', $variant->product->tenant_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('catalog.manage') || $user->hasPermissionTo('store.products.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TenantProductVariant $variant): bool
    {
        return $user->hasPermissionTo('catalog.manage') || $user->hasPermissionToOnTenant('store.products.manage', $variant->product->tenant_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TenantProductVariant $variant): bool
    {
        return $user->hasPermissionTo('catalog.manage') || $user->hasPermissionToOnTenant('store.products.manage', $variant->product->tenant_id);
    }
}
