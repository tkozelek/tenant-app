<?php

namespace App\Policies;

use App\Models\User;
use App\Models\XmlFeed;
use Filament\Facades\Filament;

class XmlFeedPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('xml_feeds.view_any')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.xml_feeds.view_any', $tenant->id);
        }

        return false;
    }

    public function view(User $user, XmlFeed $xmlFeed): bool
    {
        return $user->hasPermissionTo('xml_feeds.view_any')
            || $user->hasPermissionToOnTenant('tenant.xml_feeds.view_any', $xmlFeed->tenant_id);
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('xml_feeds.create')) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant) {
            return $user->hasPermissionToOnTenant('tenant.xml_feeds.create', $tenant->id);
        }

        return false;
    }

    public function update(User $user, XmlFeed $xmlFeed): bool
    {
        return $user->hasPermissionTo('xml_feeds.update')
            || $user->hasPermissionToOnTenant('tenant.xml_feeds.update', $xmlFeed->tenant_id);
    }

    public function delete(User $user, XmlFeed $xmlFeed): bool
    {
        return $user->hasPermissionTo('xml_feeds.delete')
            || $user->hasPermissionToOnTenant('tenant.xml_feeds.delete', $xmlFeed->tenant_id);
    }

    public function restore(User $user, XmlFeed $xmlFeed): bool
    {
        return $user->hasPermissionTo('xml_feeds.delete')
            || $user->hasPermissionToOnTenant('tenant.xml_feeds.delete', $xmlFeed->tenant_id);
    }

    public function forceDelete(User $user, XmlFeed $xmlFeed): bool
    {
        return $user->hasPermissionTo('xml_feeds.delete')
            || $user->hasPermissionToOnTenant('tenant.xml_feeds.delete', $xmlFeed->tenant_id);
    }
}
