<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQuantityPrice extends Model
{
    protected $fillable = [
        'tenant_product_id', 'quantity', 'price',
    ];
}
