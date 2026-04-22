<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantAccess
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(401);
        }

        $tenant = $request->route('tenant');

        if (! $tenant) {
            abort(404);
        }
        if (! $tenant->is_active) {
            abort(403);
        }

        if ($tenant->owner_id === $user->id) {
            return $next($request);
        }

        $membership = $tenant->users()
            ->where('users.id', $user->id)
            ->withPivot('role')
            ->first();

        if (! $membership) {
            abort(403, 'You are not a member of this tenant.');
        }
        if (! $tenant->is_active) {
            abort(403, 'This tenant is currently inactive.');
        }

        if (! empty($roles)) {
            $userRole = $membership->pivot->role ?? null;

            if (! in_array($userRole, $roles)) {
                abort(403, 'You do not have the required role in this tenant.');
            }
        }

        return $next($request);
    }
}
