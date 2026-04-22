<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use Filament\Facades\Filament;

class CouponPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('coupons.view_any')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.coupons.view_any', $tenant->id);
        }

        return false;
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.view_any')
            || $user->hasPermissionToOnTenant('tenant.coupons.view_any', $coupon->tenant_id);
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('coupons.create')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.coupons.create', $tenant->id);
        }

        return false;
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.update')
            || $user->hasPermissionToOnTenant('tenant.coupons.update', $coupon->tenant_id);
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.delete')
            || $user->hasPermissionToOnTenant('tenant.coupons.delete', $coupon->tenant_id);
    }

    public function restore(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.delete')
            || $user->hasPermissionToOnTenant('tenant.coupons.delete', $coupon->tenant_id);
    }

    public function forceDelete(User $user, Coupon $coupon): bool
    {
        return $user->hasPermissionTo('coupons.delete')
            || $user->hasPermissionToOnTenant('tenant.coupons.delete', $coupon->tenant_id);
    }

    public function export(User $user): bool
    {
        if ($user->hasPermissionTo('coupons.export')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.coupons.export', $tenant->id);
        }

        return false;
    }

    public function import(User $user): bool
    {
        return $user->hasPermissionTo('coupons.import');
    }
}
