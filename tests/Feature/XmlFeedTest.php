<?php

namespace Tests\Feature;

use App\Enums\XmlFeedPortal;
use App\Models\Tenant;
use App\Models\XmlFeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class XmlFeedTest extends TestCase
{
    use RefreshDatabase;

    private function makeFeed(Tenant $tenant, array $overrides = []): XmlFeed
    {
        return XmlFeed::create(array_merge([
            'tenant_id' => $tenant->id,
            'name' => 'Test Feed',
            'portal' => XmlFeedPortal::Generic,
            'is_active' => true,
            'include_out_of_stock' => true,
            'currency' => 'EUR',
        ], $overrides));
    }

    public function test_valid_feed_returns_xml_response(): void
    {
        $tenant = Tenant::factory()->create();
        $feed = $this->makeFeed($tenant);

        $this->get("/feed/{$tenant->slug}/{$feed->token}.xml")
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=utf-8');
    }

    public function test_feed_with_wrong_token_returns_404(): void
    {
        $tenant = Tenant::factory()->create();
        $this->makeFeed($tenant);

        $this->get("/feed/{$tenant->slug}/wrong-token.xml")
            ->assertNotFound();
    }

    public function test_inactive_feed_returns_404(): void
    {
        $tenant = Tenant::factory()->create();
        $feed = $this->makeFeed($tenant, ['is_active' => false]);

        $this->get("/feed/{$tenant->slug}/{$feed->token}.xml")
            ->assertNotFound();
    }

    public function test_feed_token_from_different_tenant_returns_404(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $feed = $this->makeFeed($tenantB);

        $this->get("/feed/{$tenantA->slug}/{$feed->token}.xml")
            ->assertNotFound();
    }

    public function test_token_is_auto_generated_on_creation(): void
    {
        $tenant = Tenant::factory()->create();
        $feed = $this->makeFeed($tenant);

        $this->assertNotEmpty($feed->token);
        $this->assertEquals(48, strlen($feed->token));
    }
}
