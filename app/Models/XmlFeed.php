<?php

namespace App\Models;

use App\Enums\XmlFeedPortal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class XmlFeed extends Model
{

    protected $fillable = [
        'tenant_id',
        'name',
        'token',
        'portal',
        'is_active',
        'include_out_of_stock',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'portal' => XmlFeedPortal::class,
            'is_active' => 'boolean',
            'include_out_of_stock' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (XmlFeed $feed): void {
            if (empty($feed->token)) {
                $feed->token = Str::random(48);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'xml_feed_category');
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(TenantProductVariant::class, 'xml_feed_product_variant');
    }
}
