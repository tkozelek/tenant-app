<?php

namespace App\Models;

use App\Observers\BundleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
            ->logOnly(['name', 'slug', 'description', 'is_active', 'url'])
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
        'is_active',
        'url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function activePriceHistory(): HasOne
    {
        return $this->hasOne(BundlePriceHistory::class, 'bundle_id')
            ->where('valid_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', now());
            })
            ->orderBy('valid_from', 'desc')
            ->orderBy('created_at', 'desc');
    }

    protected function currentPrice(): Attribute
    {
        return Attribute::make(
            get: fn (): ?float => $this->activePriceHistory?->price !== null
                ? (float) $this->activePriceHistory->price
                : null
        );
    }

    protected function currentOriginalPrice(): Attribute
    {
        return Attribute::make(
            get: fn (): ?float => $this->activePriceHistory?->original_price !== null
                ? (float) $this->activePriceHistory->original_price
                : null
        );
    }

    protected function currentPriceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->currentPrice !== null
                ? number_format($this->currentPrice, 2, ',', ' ').' €'
                : '-'
        );
    }

    protected function currentOriginalPriceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->currentOriginalPrice !== null
                ? number_format($this->currentOriginalPrice, 2, ',', ' ').' €'
                : '-'
        );
    }
}
