<?php

namespace Tests\Feature\Api\V1;

use App\Models\Bundle;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BundleApiTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->token = $this->tenant->createToken('test', ['api.bundles.read'])->plainTextToken;
    }

    private function makeBundle(Tenant $tenant, bool $active = true): Bundle
    {
        $owner = User::find($tenant->owner_id);
        $this->actingAs($owner);

        return Bundle::create([
            'tenant_id' => $tenant->id,
            'name' => fake()->words(3, true),
            'slug' => Str::slug(fake()->unique()->words(3, true)),
            'price' => 49.99,
            'original_price' => 59.99,
            'is_active' => $active,
        ]);
    }

    public function test_index_returns_only_active_bundles_for_tenant(): void
    {
        $active = $this->makeBundle($this->tenant, true);
        $this->makeBundle($this->tenant, false);

        $otherTenant = Tenant::factory()->create();
        $this->makeBundle($otherTenant, true);

        $response = $this->withToken($this->token)
            ->getJson("/api/v1/{$this->tenant->slug}/bundles")
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($active->id, $ids);
        $this->assertCount(1, $ids);
    }

    public function test_show_returns_bundle_for_correct_tenant(): void
    {
        $bundle = $this->makeBundle($this->tenant);

        $this->withToken($this->token)
            ->getJson("/api/v1/{$this->tenant->slug}/bundles/{$bundle->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $bundle->id);
    }

    public function test_show_returns_404_for_bundle_belonging_to_another_tenant(): void
    {
        $otherTenant = Tenant::factory()->create();
        $bundle = $this->makeBundle($otherTenant);

        $this->withToken($this->token)
            ->getJson("/api/v1/{$this->tenant->slug}/bundles/{$bundle->id}")
            ->assertNotFound();
    }

    public function test_bundles_require_bundles_read_permission(): void
    {
        $tokenWithout = $this->tenant->createToken('no-perm', [])->plainTextToken;

        $this->withToken($tokenWithout)
            ->getJson("/api/v1/{$this->tenant->slug}/bundles")
            ->assertStatus(403);
    }
}
