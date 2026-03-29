<?php

namespace App\Models;

use App\Observers\TenantProductVariantObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([TenantProductVariantObserver::class])]
class TenantProductVariant extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\TenantProductVariantFactory> */
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'ean', 'stock_quantity'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('variant');
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->tenant_id = $this->product?->tenant_id;
    }

    protected $fillable = [
        'name',
        'tenant_product_id',
        'sku',
        'ean',
        'stock_quantity',
    ];

    protected function casts(): array
    {
        return [
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

    public function quantityPrices(): HasMany
    {
        return $this->hasMany(ProductQuantityPrice::class, 'tenant_product_variant_id')
            ->orderBy('min_quantity');
    }

    public function activePriceHistory(): HasOne
    {
        return $this->hasOne(PriceHistory::class, 'tenant_product_variant_id')
            ->where('valid_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', now());
            })
            ->orderBy('valid_from', 'desc')
            ->orderBy('created_at', 'desc');
    }

    protected function currentPrice(): Attribute
    {
        return Attribute::make(
            get: fn (): ?float => $this->activePriceHistory?->price !== null
                ? (float) $this->activePriceHistory->price
                : null
        );
    }

    protected function currentOriginalPrice(): Attribute
    {
        return Attribute::make(
            get: fn (): ?float => $this->activePriceHistory?->original_price !== null
                ? (float) $this->activePriceHistory->original_price
                : null
        );
    }

    protected function currentPriceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->currentPrice !== null
                ? number_format($this->currentPrice, 2, ',', ' ').' €'
                : '-'
        );
    }

    protected function currentOriginalPriceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->currentOriginalPrice !== null
                ? number_format($this->currentOriginalPrice, 2, ',', ' ').' €'
                : '-'
        );
    }

    public function bundleItems(): HasMany
    {
        return $this->hasMany(BundleItem::class, 'tenant_product_variant_id');
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(
            Bundle::class,
            'bundle_items',
            'tenant_product_variant_id',
            'bundle_id'
        );
    }

    public function globalProduct(): HasOneThrough
    {
        return $this->hasOneThrough(
            GlobalProduct::class,
            TenantProduct::class,
            'id',
            'id',
            'tenant_product_id',
            'global_product_id'
        );
    }
}
