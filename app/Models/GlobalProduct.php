<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class GlobalProduct extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'description', 'is_active', 'category_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('global_product');
    }

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function globalProductAttributes(): HasMany
    {
        return $this->hasMany(GlobalProductAttribute::class)
            ->with(['attribute', 'attributeValue']);
    }

    public function tenantProducts(): HasMany
    {
        return $this->hasMany(TenantProduct::class);
    }

    public function variants(): HasManyThrough
    {
        return $this->hasManyThrough(
            TenantProductVariant::class,
            TenantProduct::class,
            'global_product_id',
            'tenant_product_id',
            'id',
            'id'
        );
    }

    public function averageVariantPrice(): ?float
    {
        return $this->variants()->avg('tenant_product_variants.price');
    }
}
