<?php

namespace App\Filament\Admin\Resources\TenantProducts\RelationManagers\components;

use App\Filament\Components\ProductAttributesSection;
use App\Models\TenantProduct;
use Filament\Schemas\Components\Section;
use Livewire\Component;

class VariantAttributesSection
{
    public static function make(): Section
    {
        return ProductAttributesSection::make(
            relationship: 'variantAttributes',
            categoryIdResolver: function (Component $livewire): ?int {
                if (method_exists($livewire, 'getOwnerRecord') && $livewire->getOwnerRecord()) {
                    return $livewire->getOwnerRecord()
                        ?->globalProduct
                        ?->category_id;
                }

                $tenantProductId = $livewire->data['tenant_product_id'] ?? null;

                if (! $tenantProductId) {
                    return null;
                }

                return TenantProduct::find($tenantProductId)?->globalProduct?->category_id;
            },
        );
    }
}
