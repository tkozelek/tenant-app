<?php

namespace Tests\Feature\Api\V1;

use App\Models\Tenant;
use App\Models\TenantProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(Tenant $tenant, array $abilities = ['api.products.read']): string
    {
        return $tenant->createToken('test', $abilities)->plainTextToken;
    }

    public function test_index_returns_only_active_products_for_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $token = $this->tokenFor($tenant);

        $active = TenantProduct::factory()->for($tenant)->create(['is_active' => true]);
        TenantProduct::factory()->for($tenant)->create(['is_active' => false]);

        $otherTenant = Tenant::factory()->create();
        TenantProduct::factory()->for($otherTenant)->create(['is_active' => true]);

        $response = $this->withToken($token)
            ->getJson("/api/v1/{$tenant->slug}/products")
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($active->id, $ids);
        $this->assertCount(1, $ids);
    }

    public function test_show_returns_product_for_correct_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $token = $this->tokenFor($tenant);
        $product = TenantProduct::factory()->for($tenant)->create();

        $this->withToken($token)
            ->getJson("/api/v1/{$tenant->slug}/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_show_returns_404_for_product_belonging_to_another_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $token = $this->tokenFor($tenant);

        $otherTenant = Tenant::factory()->create();
        $product = TenantProduct::factory()->for($otherTenant)->create();

        $this->withToken($token)
            ->getJson("/api/v1/{$tenant->slug}/products/{$product->id}")
            ->assertNotFound();
    }
}
