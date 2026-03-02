<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlobalProductRequest extends Model
{
    /** @use HasFactory<\Database\Factories\GlobalProductRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'requested_by_user_id',
        'status',
        'suggested_name',
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
        return $this->belongsTo(User::class, 'requested_by_user_id');
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
