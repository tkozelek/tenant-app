<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CheckCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'order_amount' => ['nullable', 'numeric', 'min:0'],
            'variant_id' => ['nullable', 'integer', 'exists:tenant_product_variants,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ];
    }
}
