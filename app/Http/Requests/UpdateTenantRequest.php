<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('tenant'));
    }

    public function rules(): array
    {
        $tenant = $this->route('tenant');

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,'.$tenant->id,
            'website_url' => 'nullable|url|max:2048',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'is_public' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
