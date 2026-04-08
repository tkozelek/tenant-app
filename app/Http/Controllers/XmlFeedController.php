<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\XmlFeed;
use App\Services\XmlFeedService;
use Illuminate\Http\Response;

class XmlFeedController extends Controller
{
    public function __construct(private readonly XmlFeedService $xmlFeedService) {}

    public function show(Tenant $tenant, string $token): Response
    {
        $feed = XmlFeed::query()
            ->where('tenant_id', $tenant->id)
            ->where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $xml = $this->xmlFeedService->generate($feed);

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
