<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BundleItem extends Model
{
    protected $fillable = [
        'tenant_product_variant_id',
        'bundle_id',
        'variant_id',
        'quantity'
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(TenantProductVariant::class, 'variant_id');
    }
}
