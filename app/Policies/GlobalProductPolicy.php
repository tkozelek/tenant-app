<?php

namespace App\Policies;

use App\Models\GlobalProduct;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GlobalProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Both platform admins and tenant users can view global products (catalog)
        return $user->hasPermissionTo('catalog.manage') || $user->hasPermissionTo('store.products.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GlobalProduct $globalProduct): bool
    {
        return $user->hasPermissionTo('catalog.manage') || $user->hasPermissionTo('store.products.manage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('catalog.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GlobalProduct $globalProduct): bool
    {
        return $user->hasPermissionTo('catalog.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GlobalProduct $globalProduct): bool
    {
        return $user->hasPermissionTo('catalog.manage');
    }
}
