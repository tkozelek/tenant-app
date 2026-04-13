<?php

namespace App\Models;

use App\Observers\BundleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([BundleObserver::class])]
class Bundle extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('bundles')
            ->useDisk('public');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'description', 'price', 'original_price', 'is_active', 'url'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('bundle');
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->tenant_id = $this->tenant_id;
    }

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'is_active',
        'url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BundleItem::class, 'bundle_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(BundlePriceHistory::class, 'bundle_id');
    }
}
