<?php

namespace App\Models;

use App\Observers\TenantProductVariantObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([TenantProductVariantObserver::class])]
class TenantProductVariant extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\TenantProductVariantFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'tenant_product_id',
        'sku',
        'ean',
        'price',
        'original_price',
        'stock_quantity',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'product_variant_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(TenantProduct::class, 'tenant_product_id');
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class, 'tenant_product_variant_id')->orderBy('created_at');
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_product_variant');
    }

    public function variantAttributes(): HasMany
    {
        return $this->hasMany(VariantAttribute::class, 'tenant_product_variant_id')
            ->with(['attribute', 'attributeValue']);
    }
}
