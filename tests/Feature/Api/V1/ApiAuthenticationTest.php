<?php

namespace Tests\Feature\Api\V1;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_without_token_returns_401(): void
    {
        $tenant = Tenant::factory()->create();

        $this->getJson("/api/v1/{$tenant->slug}/products")
            ->assertStatus(401)
            ->assertJson(['message' => 'API token missing.']);
    }

    public function test_request_with_invalid_token_returns_401(): void
    {
        $tenant = Tenant::factory()->create();

        $this->withToken('invalid-token')
            ->getJson("/api/v1/{$tenant->slug}/products")
            ->assertStatus(401)
            ->assertJson(['message' => 'Invalid API token.']);
    }

    public function test_request_with_expired_token_returns_401(): void
    {
        $tenant = Tenant::factory()->create();
        $token = $tenant->createToken('test', [], now()->subMinute())->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/v1/{$tenant->slug}/products")
            ->assertStatus(401)
            ->assertJson(['message' => 'API token has expired.']);
    }

    public function test_token_from_different_tenant_returns_401(): void
    {
        $tenant = Tenant::factory()->create();
        $otherTenant = Tenant::factory()->create();
        $token = $otherTenant->createToken('other', ['api.products.read'])->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/v1/{$tenant->slug}/products")
            ->assertStatus(401)
            ->assertJson(['message' => 'Invalid API token.']);
    }

    public function test_valid_token_without_required_permission_returns_403(): void
    {
        $tenant = Tenant::factory()->create();
        $token = $tenant->createToken('test', [])->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/v1/{$tenant->slug}/products")
            ->assertStatus(403);
    }

    public function test_valid_token_with_correct_permission_returns_200(): void
    {
        $tenant = Tenant::factory()->create();
        $token = $tenant->createToken('test', ['api.products.read'])->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/v1/{$tenant->slug}/products")
            ->assertStatus(200);
    }
}
