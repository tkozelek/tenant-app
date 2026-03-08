<?php

namespace App\Policies;

use App\Models\GlobalProduct;
use App\Models\User;

class GlobalProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('products.view_any');
    }

    public function view(User $user, GlobalProduct $globalProduct): bool
    {
        return $user->hasPermissionTo('products.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('products.create');
    }

    public function update(User $user, GlobalProduct $globalProduct): bool
    {
        return $user->hasPermissionTo('products.update');
    }

    public function delete(User $user, GlobalProduct $globalProduct): bool
    {
        return $user->hasPermissionTo('products.delete');
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
