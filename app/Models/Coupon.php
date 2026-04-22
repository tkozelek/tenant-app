<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Coupon extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'discount_type', 'value', 'min_order_amount', 'usage_limit', 'starts_at', 'expires_at', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('coupon');
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->tenant_id = $this->tenant_id;
    }

    protected $fillable = [
        'tenant_id', 'code', 'description', 'discount_type', 'value',
        'min_order_amount', 'usage_limit', 'used_count',
        'starts_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(TenantProductVariant::class, 'coupon_product_variant');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    public function isValid(): bool
    {
        return $this->is_active &&
            now()->between($this->starts_at, $this->expires_at) &&
            ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function appliesToVariant(TenantProductVariant $variant): bool
    {
        $hasVariantRestrictions = $this->productVariants()->exists();
        $hasCategoryRestrictions = $this->categories()->exists();

        if (! $hasVariantRestrictions && ! $hasCategoryRestrictions) {
            return true;
        }

        if ($hasVariantRestrictions && $this->productVariants()->where('tenant_product_variants.id', $variant->id)->exists()) {
            return true;
        }

        if ($hasCategoryRestrictions) {
            $category = $variant->load('product.globalProduct.category')->product?->globalProduct?->category;

            if ($category) {
                $applicableCategoryIds = $this->categories()
                    ->pluck('categories.id')
                    ->flatMap(fn (int $id) => Category::find($id)?->subtreeCategoryIds() ?? [$id])
                    ->unique()
                    ->all();

                if (in_array($category->id, $applicableCategoryIds)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function appliesToCategory(Category $category): bool
    {
        $hasCategoryRestrictions = $this->categories()->exists();

        if (! $this->productVariants()->exists() && ! $hasCategoryRestrictions) {
            return true;
        }

        if ($hasCategoryRestrictions) {
            $applicableCategoryIds = $this->categories()
                ->pluck('categories.id')
                ->flatMap(fn (int $id) => Category::find($id)?->subtreeCategoryIds() ?? [$id])
                ->unique()
                ->all();

            if (in_array($category->id, $applicableCategoryIds)) {
                return true;
            }
        }

        return false;
    }
}
