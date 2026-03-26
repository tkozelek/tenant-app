<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TenantProduct extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\TenantProductFactory> */
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'description', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('tenant_product');
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->tenant_id = $this->tenant_id;
    }

    protected $fillable = [
        'tenant_id',
        'global_product_id',
        'global_product_request_id',
        'name',
        'slug',
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

    public function category(): HasOneThrough
    {
        return $this->hasOneThrough(Category::class, GlobalProduct::class, 'id', 'id', 'global_product_id', 'category_id');
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

    public function priceHistories(): HasManyThrough
    {
        return $this->hasManyThrough(PriceHistory::class, TenantProductVariant::class, 'tenant_product_id', 'tenant_product_variant_id');
    }

    public function getLowestCurrentPriceAttribute(): ?float
    {
        $activePriceSubquery = PriceHistory::query()
            ->select('price')
            ->whereColumn('tenant_product_variant_id', 'tenant_product_variants.id')
            ->active()
            ->latest('valid_from')
            ->limit(1);

        return $this->variants()
            ->where('stock_quantity', '>', 0)
            ->addSelect(['active_price' => $activePriceSubquery])
            ->pluck('active_price')
            ->filter()
            ->min();
    }

    public function getCheapestVariant(): ?TenantProductVariant
    {
        $activePriceSubquery = PriceHistory::query()
            ->select('price')
            ->whereColumn('tenant_product_variant_id', 'tenant_product_variants.id')
            ->active()
            ->latest('valid_from')
            ->limit(1);

        return $this->variants()
            ->where('stock_quantity', '>', 0)
            ->addSelect(['active_price' => $activePriceSubquery])
            ->orderByRaw('active_price IS NULL, active_price ASC')
            ->first();
    }
}
