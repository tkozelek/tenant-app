<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdjustStockRequest;
use App\Models\Tenant;
use App\Models\TenantProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function store(AdjustStockRequest $request, Tenant $tenant, TenantProductVariant $variant): JsonResponse
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::StockWrite->value),
            403,
            'Missing permission: '.ApiPermission::StockWrite->value
        );

        abort_unless($variant->product->tenant_id === $tenant->id, 404);

        $data = $request->validated();

        DB::transaction(function () use ($variant, $data): void {
            $variant->stockHistories()->create([
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
                'user_id' => null,
            ]);

            $variant->increment('stock_quantity', $data['quantity']);
        });

        return response()->json([
            'message' => 'Stock adjusted successfully.',
            'stock_quantity' => $variant->fresh()->stock_quantity,
        ]);
    }
}
