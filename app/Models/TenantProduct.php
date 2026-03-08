<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TenantProduct extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\TenantProductFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'tenant_id',
        'global_product_id',
        'global_product_request_id',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function globalProduct(): BelongsTo
    {
        return $this->belongsTo(GlobalProduct::class);
    }

    public function globalProductRequest(): BelongsTo
    {
        return $this->belongsTo(GlobalProductRequest::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(TenantProductVariant::class);
    }

    public function getLowestCurrentPriceAttribute(): ?float
    {
        return TenantProductVariant::whereHas('tenantProduct', function ($query) {
            $query->where('global_product_id', $this->id);
        })
            ->where('stock_quantity', '>', 0)
            ->min('price');
    }

    /**
     * Vráti najlacnejsi variant tenanta
     */
    public function getCheapestVariant(): ?TenantProductVariant
    {
        return TenantProductVariant::whereHas('tenantProduct', function ($query) {
            $query->where('global_product_id', $this->id);
        })
            ->where('stock_quantity', '>', 0)
            ->orderBy('price', 'asc')
            ->first();
    }
}
