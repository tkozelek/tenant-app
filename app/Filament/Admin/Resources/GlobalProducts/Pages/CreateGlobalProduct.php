<?php

namespace App\Filament\Admin\Resources\GlobalProducts\Pages;

use App\Filament\Admin\Resources\GlobalProducts\GlobalProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlobalProduct extends CreateRecord
{
    protected static string $resource = GlobalProductResource::class;

    protected function afterCreate(): void
    {
        foreach ($this->data['globalProductAttributes'] ?? [] as $item) {
            $attributeId = $item['attribute_id'] ?? null;

            if (! $attributeId) {
                continue;
            }

            $valueIds = $item['attribute_value_id'] ?? [];

            if (is_array($valueIds) && count($valueIds) > 0) {
                foreach ($valueIds as $valueId) {
                    $this->record->globalProductAttributes()->create([
                        'attribute_id' => $attributeId,
                        'attribute_value_id' => $valueId,
                    ]);
                }
            } else {
                $this->record->globalProductAttributes()->create([
                    'attribute_id' => $attributeId,
                    'custom_value' => $item['custom_value'] ?? null,
                ]);
            }
        }
    }
}
