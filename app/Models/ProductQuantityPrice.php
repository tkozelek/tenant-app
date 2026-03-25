<?php

namespace App\Models;

use App\Observers\ProductQuantityPriceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([ProductQuantityPriceObserver::class])]
class ProductQuantityPrice extends Model
{
    use SoftDeletes;

    protected $fillable = ['tenant_product_variant_id', 'min_quantity', 'max_quantity', 'price', 'valid_from', 'valid_to'];

    protected function casts(): array
    {
        return [
            'min_quantity' => 'integer',
            'max_quantity' => 'integer',
            'price' => 'decimal:2',
            'valid_from' => 'datetime',
            'valid_to' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
