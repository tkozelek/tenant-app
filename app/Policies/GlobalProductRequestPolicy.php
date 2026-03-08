<?php

namespace App\Policies;

use App\Models\GlobalProductRequest;
use App\Models\User;

class GlobalProductRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('catalog.manage')) {
            return true;
        }

        return $user->hasPermissionTo('tenant.products.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GlobalProductRequest $globalProductRequest): bool
    {
        if ($user->hasPermissionTo('catalog.manage')) {
            return true;
        }

        return $user->hasPermissionToOnTenant('tenant.products.manage', $globalProductRequest->tenant_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('tenant.products.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GlobalProductRequest $globalProductRequest): bool
    {
        if ($user->hasPermissionTo('catalog.manage')) {
            return true;
        }

        return $user->hasPermissionToOnTenant('tenant.products.manage', $globalProductRequest->tenant_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GlobalProductRequest $globalProductRequest): bool
    {
        return $user->hasPermissionTo('catalog.manage');
    }
}
