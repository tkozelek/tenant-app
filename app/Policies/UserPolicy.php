<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->hasPermissionTo('users.view_any')) {
            return true;
        }

        // Users can view their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->hasPermissionTo('users.update')) {
            return true;
        }

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->hasPermissionTo('users.delete')) {
            return true;
        }

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can export any models.
     */
    public function exportAny(User $user): bool
    {
        return $user->hasPermissionTo('users.export');
    }

    /**
     * Determine whether the user can import any models.
     */
    public function importAny(User $user): bool
    {
        return $user->hasPermissionTo('users.import');
    }
}
