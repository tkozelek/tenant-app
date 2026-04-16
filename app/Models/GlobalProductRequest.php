<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class GlobalProductRequest extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\GlobalProductRequestFactory> */
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'admin_note', 'suggested_name', 'suggested_description', 'suggested_category_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('product_request');
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->tenant_id = $this->tenant_id;
    }

    protected $fillable = [
        'tenant_id',
        'user_id',
        'status',
        'suggested_name',
        'suggested_description',
        'suggested_category_id',
        'admin_note',
        'created_global_product_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function suggestedCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'suggested_category_id');
    }

    public function createdGlobalProduct(): BelongsTo
    {
        return $this->belongsTo(GlobalProduct::class, 'created_global_product_id');
    }
}
