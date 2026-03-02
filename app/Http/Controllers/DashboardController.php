<?php

namespace App\Http\Controllers;

use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $ownedTenants = Tenant::with('media')
            ->where('owner_id', $user->id)
            ->get();

        $otherTenants = Tenant::with('media')
            ->whereIn('id', function ($query) use ($user) {
                $query->select('tenant_id')
                    ->from(config('permission.table_names.model_has_roles'))
                    ->where('model_id', $user->id)
                    ->where('model_type', get_class($user));
            })
            ->where('owner_id', '!=', $user->id)
            ->get();

        return view('dashboard.dashboard', [
            'ownedTenants' => $ownedTenants,
            'otherTenants' => $otherTenants,
            'title' => 'Dashboard',
        ]);
    }
}
