<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeValue extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeValueFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
        'sort_order',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(TenantProductVariant::class, 'attribute_value_tenant_product_variant');
    }

    public function getFullLabelAttribute(): string
    {
        return "{$this->attribute->name}: {$this->value}";
    }
}
