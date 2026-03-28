<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'parent_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('category');
    }

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function globalProducts(): HasMany
    {
        return $this->hasMany(GlobalProduct::class, 'category_id');
    }

    public function allDescendants(): Collection
    {
        $descendants = new Collection;
        $parentIds = [$this->id];

        while (!empty($parentIds)) {
            $children = Category::query()->whereIn('parent_id', $parentIds)->get();

            if ($children->isEmpty()) {
                break;
            }

            $descendants = $descendants->merge($children);
            $parentIds = $children->pluck('id')->all();
        }

        return $descendants;
    }

    public function subtreeCategoryIds(): array
    {
        return $this->allDescendants()
            ->pluck('id')
            ->prepend($this->id)
            ->all();
    }

    public function allGlobalProducts(): \Illuminate\Database\Eloquent\Builder
    {
        return GlobalProduct::query()->whereIn('category_id', $this->subtreeCategoryIds());
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'category_attribute');
    }

    public function allAttributes(): \Illuminate\Support\Collection
    {
        $categoryIds = $this->subtreeCategoryIds();

        return Attribute::query()
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->with(['attributeValues' => fn ($q) => $q->orderBy('sort_order')])
            ->get()
            ->unique('id')
            ->values();
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_category');
    }
}
