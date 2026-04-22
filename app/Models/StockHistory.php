<?php

namespace App\Models;

use App\Enums\StockHistoryType;
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

    protected function casts(): array
    {
        return [
            'type' => StockHistoryType::class,
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(TenantProductVariant::class, 'product_variant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
