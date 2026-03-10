<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('coupons.view_any');
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('coupons.create');
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.update');
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.delete');
    }

    public function restore(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.delete');
    }

    public function forceDelete(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.delete');
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('coupons.export');
    }

    public function import(User $user): bool
    {
        return $user->hasPermissionTo('coupons.import');
    }
}
