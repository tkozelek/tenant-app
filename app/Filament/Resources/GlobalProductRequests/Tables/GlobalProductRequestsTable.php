<?php

namespace App\Filament\Resources\GlobalProductRequests\Tables;

use App\Models\Category;
use App\Models\GlobalProduct;
use App\Models\GlobalProductRequest;
use App\View\Components\Badge;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Gemini\Enums\ModelVariation;
use Gemini\GeminiHelper;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GlobalProductRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('suggested_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('tenant.name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        "pending" => "Čaká",
                        "approved" => "Prijatý",
                        "rejected" => "Zamietnutý",
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->placeholder('Vyberte')
                    ->options([
                        'pending' => 'Čaká',
                        'approved' => 'Prijatý',
                        'rejected' => 'Zamietnutý',
                    ])->default('pending')
            ])
            ->recordActions([
                EditAction::make()
                    ->label(function (GlobalProductRequest $record) {
                        return $record->status === 'approved' ? 'View' : 'Edit';
                    }),

                Action::make('approve_and_create')
                    ->label('Prijať')
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
                            ->label("Popis produktu")
                            ->columnSpanFull()
                            ->default(fn (GlobalProductRequest $record) => $record->suggested_description ?? null)
                            ->hintAction(
                                Action::make('generate_description')
                                    ->icon("heroicon-o-pencil")
                                    ->label("Generovať popis")
                                    ->action(function (Get $get, Set $set) {
                                        $productName = $get('name');
                                        $currentDesc = $get('description');
                                        if (empty($productName)) {
                                            Notification::make()->warning()->title("Zadajte názov produktu")->send();
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
                                            Log::error('Error generating description: ' . $e->getMessage());
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
                    }),
                Action::make('reject')
                    ->label('Odmietnuť')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (GlobalProductRequest $record) => $record->status === 'pending')
                    ->schema([
                        Textarea::make('admin_note')
                            ->label('Poznámka (admin)')
                            ->required()
                    ])
                    ->action(function (array $data, GlobalProductRequest $record) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_note' => $data['admin_note']
                            ]);

                        Cache::forget('global_product_requests_count');
                        Notification::make()->title('Request odmietnutý.')->danger()->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
