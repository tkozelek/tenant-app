<?php

namespace App\Filament\Resources\TenantProducts\RelationManagers;

use App\Models\TenantProductVariant;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $recordTitleAttribute = 'sku';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail a zasoby')
                    ->schema([
                        TextInput::make('name')
                            ->label('Názov variantu')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('sku')
                            ->label('SKU (Skladové číslo)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        TextInput::make('ean')
                            ->label('EAN kód')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        TextInput::make('stock_quantity')
                            ->label('Skladová zásoba')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->disabledOn('edit')
                            ->dehydrated()
                            ->helperText(fn (string $operation): string => $operation === 'edit'
                                ? 'Pre upravu skladu stlacte tlacidlo uprava skladu.'
                                : '')
                            ->columnSpan(1),
                    ]),
                Section::make('Cenotvorba')
                    ->schema([
                        TextInput::make('price')
                            ->label('Aktuálna cena')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->step('0.01'),

                        TextInput::make('original_price')
                            ->label('Pôvodná cena (pred zľavou)')
                            ->numeric()
                            ->prefix('€')
                            ->step('0.01')
                            ->helperText('Vyplňte, ak je produkt v zľave.'),
                    ]),

                Section::make('Atribúty a obrázky')
                    ->schema([
                        Select::make('attributeValues')
                            ->relationship('attributeValues', 'value')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Hodnoty atribútov')
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('media')
                            ->collection('tenant_product_variants')
                            ->multiple()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->label('Obrázky variantu')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')
                    ->collection('variants')
                    ->label('Obrázok')
                    ->square()
                    ->limit(3),

                TextColumn::make('name')
                    ->label('Nazov')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('attributeValues.value')
                    ->label('Atribúty')
                    ->badge()
                    ->searchable()
                    ->limitList(4)
                    ->tooltip(fn ($record): string => $record->attributeValues->pluck('value')->join(', ')),

                TextColumn::make('price')
                    ->label('Cena')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('original_price')
                    ->label('Pôvodná cena')
                    ->money('EUR')
                    ->color('gray')
                    ->extraAttributes(['style' => 'text-decoration: line-through;'])
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stock_quantity')
                    ->label('Skladom')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 10 => 'success',
                        $state > 0 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Pridať variant'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('adjust_stock')
                    ->label('Sklad')
                    ->icon('heroicon-o-circle-stack')
                    ->color('warning')
                    ->schema([
                        Select::make('type')
                            ->label('Zmenit typ')
                            ->options([
                                'purchase' => 'Nákup',
                                'sale' => 'Predaj',
                                'adjustment' => 'Oprava',
                                'return' => 'Vrátenie',
                                'transfer' => 'Prevod',
                            ])
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Zmena skladu')
                            ->numeric()
                            ->required()
                            ->rules([
                                fn ($record) => function (string $attribute, $value, \Closure $fail) use ($record) {
                                    $currentStock = $record->stock_quantity;

                                    if ($value < 0 && ($currentStock + $value) < 0) {
                                        $fail("Nedostatok zasob. Nemozete odobrat viac ako je aktuálny stav ({$currentStock}).");
                                    }
                                },
                            ])
                            ->helperText('Pozitívne na pridanie kusov, negatívne na odobratie.'),
                        Textarea::make('note')
                            ->label('Note')
                            ->columnSpanFull(),
                    ])
                ->action(function (array $data, $record) {
                    // transakcia, keby nejaké zlyha
                    try {
                        DB::transaction(function () use ($record, $data) {
                            $record->stockHistories()->create([
                                'type' => $data['type'],
                                'quantity' => $data['quantity'],
                                'note' => $data['note'],
                            ]);

                            $record->increment('stock_quantity', $data['quantity']);
                        });
                    } catch (\Exception $e) {
                        Notification::make()->title('Nastala chyba.')->danger()->send();
                    }
                })->successNotificationTitle("Stav skladu zmenený úspešne.")
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
