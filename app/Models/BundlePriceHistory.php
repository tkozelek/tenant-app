<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BundlePriceHistory extends Model
{
    protected $table = 'bundle_price_history';

    protected $fillable = [
        'bundle_id',
        'price',
        'original_price',
        'valid_from',
        'valid_to',
        'user_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime'
    ];

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
