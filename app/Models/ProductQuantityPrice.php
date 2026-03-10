<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQuantityPrice extends Model
{
    protected $fillable = ['tenant_product_variant_id', 'min_quantity', 'max_quantity', 'unit_price'];
}
