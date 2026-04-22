<?php

namespace App\Policies;

use App\Models\User;
use Filament\Facades\Filament;
use Spatie\Activitylog\Models\Activity;

class ActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('tenant.activity_log.view')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.activity_log.view', $tenant->id);
        }

        return false;
    }

    public function view(User $user, Activity $activity): bool
    {
        if ($user->hasPermissionTo('tenant.activity_log.view')) {
            return true;
        }

        if ($activity->tenant_id) {
            return $user->hasPermissionToOnTenant('tenant.activity_log.view', $activity->tenant_id);
        }

        return false;
    }
}
