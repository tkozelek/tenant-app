<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearer = $request->bearerToken();

        if (! $bearer) {
            return response()->json(['message' => 'API token missing.'], 401);
        }

        $tenant = $request->route('tenant');

        // tenant v url
        if (! $tenant) {
            return response()->json(['message' => 'Tenant not found.'], 404);
        }

        $token = ApiToken::findToken($bearer);

        // tenant a tenant id
        if (! $token
            || $token->tokenable_type !== Tenant::class
            || $token->tokenable_id !== $tenant->id
        ) {
            return response()->json(['message' => 'Invalid API token.'], 401);
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            return response()->json(['message' => 'API token has expired.'], 401);
        }

        // skip timestamps
        $token->forceFill(['last_used_at' => now()])->saveQuietly();

        $request->attributes->set('api_token', $token);
        $request->attributes->set('api_tenant', $tenant);

        return $next($request);
    }
}
