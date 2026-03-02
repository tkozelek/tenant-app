<?php

namespace App\Policies;

use App\Models\GlobalProductRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GlobalProductRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Platform admins can view all requests
        if ($user->hasPermissionTo('catalog.manage')) {
            return true;
        }

        // Tenant users can view their own requests
        return $user->hasPermissionTo('store.products.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GlobalProductRequest $globalProductRequest): bool
    {
        if ($user->hasPermissionTo('catalog.manage')) {
            return true;
        }

        // Check if user belongs to the tenant that made the request AND has permission for THAT tenant
        return $user->hasPermissionToOnTenant('store.products.manage', $globalProductRequest->tenant_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('store.products.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GlobalProductRequest $globalProductRequest): bool
    {
        // Platform admins can update (approve/reject)
        if ($user->hasPermissionTo('catalog.manage')) {
            return true;
        }

        // Tenant users can update their own pending requests
        return $user->hasPermissionToOnTenant('store.products.manage', $globalProductRequest->tenant_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GlobalProductRequest $globalProductRequest): bool
    {
        return $user->hasPermissionTo('catalog.manage');
    }
}
