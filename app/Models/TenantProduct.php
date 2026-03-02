<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenantProduct extends Model
{
    /** @use HasFactory<\Database\Factories\TenantProductFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'global_product_id',
        'global_product_request_id',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function globalProduct(): BelongsTo
    {
        return $this->belongsTo(GlobalProduct::class);
    }

    public function globalProductRequest(): BelongsTo
    {
        return $this->belongsTo(GlobalProductRequest::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(TenantProductVariant::class);
    }
}
