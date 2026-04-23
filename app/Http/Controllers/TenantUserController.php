<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Cache;
use Gate;
use Illuminate\Http\Request;

class TenantUserController extends Controller
{
    public function show(Tenant $tenant)
    {
        Gate::authorize('assignRoles', $tenant);
        $roles = Cache::remember('user-roles', 60 * 60, function () {
            return Role::where('scope', 'tenant')->get();
        });

        return view('tenant.tenantuser', [
            'tenant' => $tenant,
            'roles' => $roles,
        ]);
    }

    public function store(Tenant $tenant, Request $request)
    {
        Gate::authorize('assignRoles', $tenant);

        $validated = $request->validateWithBag("tenantUserAddition{$tenant->id}", [
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', 'exists:roles,name'],
        ], [
            'email.exists' => 'Používateľ s týmto e-mailom neexistuje.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user->id === $tenant->owner_id) {
            return back()->withErrors([
                'email' => 'Tento používateľ je už majiteľom obchodu.',
            ], "tenantUserAddition{$tenant->id}");
        }

        setPermissionsTeamId($tenant->id);

        // Remove existing roles for this tenant before assigning new one
        $user->roles()->where('model_has_roles.tenant_id', $tenant->id)->detach();

        $user->assignRole($validated['role']);

        if ($user->is(auth()->user())) {
            return redirect()->route('dashboard.index')
                ->with('success', 'Upravil si sám seba, obnov stránku.');
        }

        return to_route('tenant-user.index', $tenant->slug)
            ->with('success', "Používateľ '{$user->first_name} {$user->last_name}' bol úspešne pridaný do obchodu.");
    }
}
