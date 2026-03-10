<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    protected $table = 'price_history';

    protected $fillable = [
        'tenant_product_variant_id',
        'price',
        'original_price',
        'user_id',
        'valid_from',
        'valid_to',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime'
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(TenantProductVariant::class, 'tenant_product_variant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    public function active(Builder $query): Builder
    {
        return $query->whereNull('valid_to');
    }

    #[Scope]
    public function validAt(Builder $query, $date): Builder
    {
        return $query->where('valid_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', $date);
            });
    }
}
