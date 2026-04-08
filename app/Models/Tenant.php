<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Filament\Panel\Concerns\HasBrandName;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Tenant extends Model implements HasMedia, HasName
{
    use HasApiTokens, HasBrandName, HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'description', 'short_description', 'is_public', 'owner_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('tenant');
    }

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'description',
        'short_description',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'tenant_id', 'model_id')
            ->where('model_type', User::class)
            ->withPivot(['role_id', 'model_type']);
    }

    /**
     * Get all users associated with the tenant (owner + role holders).
     */
    public function allUsers(): Builder
    {
        return User::query()
            ->where(function (Builder $query) {
                $query->where('id', $this->owner_id)
                    ->orWhereHas('roles', function ($q) {
                        $q->where('model_has_roles.tenant_id', $this->id);
                    });
            });
    }

    public function products(): HasMany
    {
        return $this->hasMany(TenantProduct::class);
    }

    public function bundles(): HasMany
    {
        return $this->hasMany(Bundle::class);
    }

    public function globalProductRequests(): HasMany
    {
        return $this->hasMany(GlobalProductRequest::class);
    }

    public function xmlFeeds(): HasMany
    {
        return $this->hasMany(XmlFeed::class);
    }

    public function getImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('images');
    }

    public function getTitleImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('titles');
    }

    public function getBrandName(): string|Htmlable
    {
        return "Tenant - {$this->name}";
    }
}
