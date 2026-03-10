<?php

namespace App\Models;

use App\Observers\BundleObserver;
use App\Observers\TenantProductVariantObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([BundleObserver::class])]
class Bundle extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BundleItem::class, 'bundle_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(BundlePriceHistory::class, 'bundle_id');
    }
}
