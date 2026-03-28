<?php

namespace App\Filament\Admin\Resources\GlobalProducts\Pages;

use App\Filament\Admin\Resources\GlobalProducts\GlobalProductResource;
use App\Filament\Admin\Resources\GlobalProducts\Schemas\actions\HistoryAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGlobalProduct extends EditRecord
{
    protected static string $resource = GlobalProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewProduct')
                ->label('Zobrazit')
                ->url(fn () => route('products.product', $this->record->slug))
                ->openUrlInNewTab(),
            HistoryAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['globalProductAttributes'] = $this->record->globalProductAttributes
            ->groupBy('attribute_id')
            ->map(fn ($items) => [
                'attribute_id' => $items->first()->attribute_id,
                'attribute_value_id' => $items->pluck('attribute_value_id')->filter()->values()->all(),
                'custom_value' => $items->first()->custom_value,
            ])
            ->all();

        // manually hydrate the repeater..
        //        dump($data);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->saveGlobalProductAttributes();
    }

    private function saveGlobalProductAttributes(): void
    {
        $this->record->globalProductAttributes()->delete();

        //        dump($this->data['globalProductAttributes']);

        foreach ($this->data['globalProductAttributes'] ?? [] as $item) {
            $attributeId = $item['attribute_id'] ?? null;

            if (! $attributeId) {
                continue;
            }

            $valueIds = $item['attribute_value_id'] ?? [];

            //            dump([$valueIds, $item['custom_value']]);

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
