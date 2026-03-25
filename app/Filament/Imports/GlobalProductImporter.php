<?php

namespace App\Filament\Imports;

use App\Models\Category;
use App\Models\GlobalProduct;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class GlobalProductImporter extends Importer
{
    protected static ?string $model = GlobalProduct::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Názov')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('slug')
                ->label('Slug')
                ->rules(['nullable', 'string', 'max:255'])
                ->castStateUsing(fn (?string $state, array $data): string => $state
                    ? $state
                    : Str::slug($data['name'] ?? '')
                ),

            ImportColumn::make('category_id')
                ->label('Kategória (slug)')
                ->requiredMapping()
                ->rules(['required'])
                ->castStateUsing(fn (string $state): ?int => Category::where('slug', $state)->value('id')),

            ImportColumn::make('description')
                ->label('Popis')
                ->rules(['nullable', 'string']),

            ImportColumn::make('is_active')
                ->label('Aktívny (1/0)')
                ->boolean()
                ->rules(['nullable', 'boolean']),
        ];
    }

    public function resolveRecord(): GlobalProduct
    {
        return GlobalProduct::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import globálnych produktov dokončený – '.Number::format($import->successful_rows).' '.str('záznam')->plural($import->successful_rows).' importovaných.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('záznam')->plural($failedRowsCount).' zlyhalo.';
        }

        return $body;
    }
}
