<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('categories.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('categories.view_any');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('categories.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('categories.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('categories.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('categories.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('categories.delete');
    }

    /**
     * Determine whether the user can export the model.
     */
    public function exportAny(User $user): bool
    {
        return $user->hasPermissionTo('categories.export');
    }

    /**
     * Determine whether the user can import the model.
     */
    public function importAny(User $user): bool
    {
        return $user->hasPermissionTo('categories.import');
    }
}
