<?php

namespace App\Filament\Resources\GlobalProductRequests\Tables\actions;

use App\Models\Category;
use App\Models\GlobalProduct;
use App\Models\GlobalProductRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApproveAndCreateAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'approve_and_create';
    }

    protected function setUp(): void
    {
        $this->label('Prijať')
            ->color('success')
            ->icon('heroicon-o-check-badge')
            ->visible(fn (GlobalProductRequest $record) => $record->status === 'pending')
            ->schema([
                TextInput::make('name')
                    ->default(fn (GlobalProductRequest $record) => $record->suggested_name)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->default(fn (GlobalProductRequest $record) => Str::slug($record->suggested_name))
                    ->rule('unique:global_products,slug'),

                Select::make('category_id')
                    ->options(Category::pluck('name', 'id'))
                    ->default(fn (GlobalProductRequest $record) => $record->suggested_category_id)
                    ->required()
                    ->label('Kategória'),

                RichEditor::make('description')
                    ->label('Popis produktu')
                    ->columnSpanFull()
                    ->default(fn (GlobalProductRequest $record) => $record->suggested_description ?? null)
                    ->hintAction(
                        Action::make('generate_description')
                            ->icon('heroicon-o-sparkles')
                            ->label('Generovať popis')
                            ->action(function (Get $get, Set $set) {
                                $productName = $get('name');
                                $currentDesc = $get('description');
                                if (empty($productName)) {
                                    Notification::make()->warning()->title('Zadajte názov produktu')->send();

                                    return;
                                }
                                try {
                                    $prompt = $currentDesc
                                        ? "Si expert na e-commerce. Tu je návrh popisu pre produkt '{$productName}': '{$currentDesc}'. Vylepši ho, aby bol profesionálny a pútavý v slovenčine. Vráť VÝHRADNE platný HTML kód."
                                        : "Si expert na e-commerce. Napíš pútavý popis pre produkt '{$productName}' v slovenčine. Vráť VÝHRADNE platný HTML kód.";

                                    $result = Gemini::generativeModel(model: 'gemini-2.5-flash')->generateContent($prompt);

                                    $generatedHtml = $result->text();

                                    $generatedHtml = preg_replace('/```html\n?(.*?)\n?```/s', '$1', $generatedHtml);
                                    $set('description', trim($generatedHtml));

                                    Cache::forget('global_product_requests_count');

                                    Notification::make()->success()->title('Popis vygenerovaný!')->send();
                                } catch (\Exception $e) {
                                    Notification::make()->danger()->title('Nepodarilo sa pripojiť k AI.')->send();
                                    Log::error('Error generating description: '.$e->getMessage());
                                }
                            })
                    ),
            ])
            ->action(function (array $data, GlobalProductRequest $record) {
                $data['is_active'] = true;
                $globalProduct = GlobalProduct::create($data);

                $record->update([
                    'status' => 'approved',
                    'created_global_product_id' => $globalProduct->id,
                ]);

                Notification::make()->title('Globalny produkt vytvoreny, request upraveny.')->success()->send();
            });
    }
}
