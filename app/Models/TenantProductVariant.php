<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

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

    protected static function booted()
    {
        static::created(function (TenantProductVariant $productVariant) {
            if ($productVariant->stock_quantity > 0) {
                $productVariant->stockHistories()->create([
                    'type' => 'adjustment',
                    'quantity' => $productVariant->stock_quantity,
                    'note' => 'initial stock',
                    'user_id' => auth()->user()->id ?? null,
                ]);
            }
        });
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'product_variant_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(TenantProduct::class, 'tenant_product_id');
    }

    public function variantAttributes(): HasMany
    {
        return $this->hasMany(VariantAttribute::class, 'tenant_product_variant_id')
            ->with(['attribute', 'attributeValue']);
    }
}
