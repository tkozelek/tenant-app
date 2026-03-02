<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TenantProductVariant extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\TenantProductVariantFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(TenantProduct::class, 'tenant_product_id');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_value_tenant_product_variant');
    }
}
