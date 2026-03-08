<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    protected $table = 'stock_history';

    protected $fillable = [
        'product_variant_id',
        'user_id',
        'type',
        'quantity',
        'note',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(TenantProductVariant::class, 'product_variant_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
}
