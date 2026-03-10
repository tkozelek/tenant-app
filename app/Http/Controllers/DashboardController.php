<?php

namespace App\Http\Controllers;

use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $user->load('roles.permissions');

        $tenants = Tenant::with([
            'media',
            'users' => fn ($query) => $query->where('users.id', $user->id),
        ])
            ->where(function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhereHas('users', fn ($q) => $q->where('users.id', $user->id));
            })
            ->get()
            ->sortBy(function ($tenant) use ($user) {
                if ($tenant->owner_id === $user->id) {
                    return 0;
                }

                $roleId = $tenant->users->first()?->pivot->role_id;

                return $roleId ?? 999;
            })
            ->values();

        return view('dashboard.dashboard', [
            'tenants' => $tenants,
            'title' => 'Dashboard',
        ]);
    }
}
