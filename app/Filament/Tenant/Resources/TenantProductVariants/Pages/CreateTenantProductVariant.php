<?php

namespace App\Filament\Tenant\Resources\TenantProductVariants\Pages;

use App\Filament\Tenant\Resources\TenantProductVariants\TenantProductVariantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantProductVariant extends CreateRecord
{
    protected static string $resource = TenantProductVariantResource::class;

    protected ?float $initialPrice = null;

    protected ?float $initialOriginalPrice = null;

    protected ?string $initialValidFrom = null;

    protected ?string $initialValidTo = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->initialPrice = isset($data['initial_price']) ? (float) $data['initial_price'] : null;
        $this->initialOriginalPrice = isset($data['initial_original_price']) ? (float) $data['initial_original_price'] : null;
        $this->initialValidTo = $data['initial_valid_to'] ?? null;
        $this->initialValidFrom = $data['initial_valid_from'] ?? null;

        unset($data['initial_price'], $data['initial_original_price'], $data['initial_valid_to'], $data['initial_valid_from']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->initialPrice === null) {
            return;
        }

        $this->record->priceHistories()->create([
            'price' => $this->initialPrice,
            'original_price' => $this->initialOriginalPrice,
            'valid_from' => $this->initialValidFrom ?? now(),
            'valid_to' => $this->initialValidTo,
            'user_id' => auth()->id(),
        ]);
    }
}
