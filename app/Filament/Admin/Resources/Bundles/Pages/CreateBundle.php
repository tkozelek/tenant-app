<?php

namespace App\Filament\Admin\Resources\Bundles\Pages;

use App\Filament\Admin\Resources\Bundles\BundleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBundle extends CreateRecord
{
    protected static string $resource = BundleResource::class;

    protected ?float $initialPrice = null;

    protected ?float $initialOriginalPrice = null;

    protected ?string $initialValidFrom = null;

    protected ?string $initialValidTo = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->initialPrice = isset($data['initial_price']) ? (float) $data['initial_price'] : null;
        $this->initialOriginalPrice = isset($data['initial_original_price']) ? (float) $data['initial_original_price'] : null;
        $this->initialValidFrom = $data['initial_valid_from'] ?? null;
        $this->initialValidTo = $data['initial_valid_to'] ?? null;

        unset($data['initial_price'], $data['initial_original_price'], $data['initial_valid_from'], $data['initial_valid_to']);

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
