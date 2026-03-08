<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    protected $fillable = [
        'tenant_id', 'code', 'discount_type', 'value',
        'min_order_amount', 'usage_limit', 'used_count',
        'starts_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:4',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(TenantProductVariant::class, 'coupon_product_variant');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    public function isValid(): bool
    {
        return $this->is_active &&
            now()->between($this->starts_at, $this->expires_at) &&
            ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }
}
