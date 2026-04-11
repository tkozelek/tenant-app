<?php

namespace App\Models;

use App\Observers\PriceHistoryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([PriceHistoryObserver::class])]
class PriceHistory extends Model
{
    use HasFactory;

    protected $table = 'price_history';

    protected $fillable = [
        'tenant_product_variant_id',
        'price',
        'original_price',
        'user_id',
        'valid_from',
        'valid_to',
        'is_flash_sale',
        'flash_sale_label',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_flash_sale' => 'boolean',
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
        return $query->where('valid_from', '<=', now())
            ->where(fn ($q) => $q->whereNull('valid_to')->orWhere('valid_to', '>=', now()));
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
