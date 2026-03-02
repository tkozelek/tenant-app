<?php

namespace App\Policies;

use App\Models\AttributeValue;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttributeValuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttributeValue $attributeValue): bool
    {
        return true;
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
    public function update(User $user, AttributeValue $attributeValue): bool
    {
        return $user->hasPermissionTo('catalog.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttributeValue $attributeValue): bool
    {
        return $user->hasPermissionTo('catalog.manage');
    }
}
