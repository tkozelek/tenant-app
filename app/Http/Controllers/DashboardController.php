<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $search = $request->get('search');

        $tenants = Tenant::with(['media'])
            ->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhereHas('users', fn ($q) => $q->where('users.id', $user->id));
            })
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderByRaw('CASE WHEN owner_id = ? THEN 0 ELSE 1 END', [$user->id])
            ->orderByRaw('(SELECT role_id FROM model_has_roles WHERE model_has_roles.tenant_id = tenants.id AND model_has_roles.model_id = ? LIMIT 1) ASC', [$user->id])
            ->paginate(12)
            ->withQueryString();

        return view('dashboard.dashboard', [
            'tenants' => $tenants,
            'search' => $search,
            'title' => 'Dashboard',
        ]);
    }
}
