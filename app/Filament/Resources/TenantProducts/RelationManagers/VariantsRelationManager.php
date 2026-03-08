<?php

namespace App\Filament\Resources\TenantProducts\RelationManagers;

use App\Filament\Resources\TenantProducts\RelationManagers\actions\AdjustStockAction;
use App\Filament\Resources\TenantProducts\RelationManagers\actions\HistoryAction;
use App\Filament\Resources\TenantProducts\RelationManagers\components\VariantAttributesSection;
use App\Models\TenantProductVariant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                            ->columnSpan(2),

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
                            ->columnSpan(2),
                    ])->columns(2),

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

                VariantAttributesSection::make(),

                Section::make('Obrázky')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->collection('tenant_product_variants')
                            ->multiple()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->label('Obrázky variantu')
                            ->columnSpanFull(),
                    ]),
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

                TextColumn::make('variantAttributesList')
                    ->label('Atribúty')
                    ->badge()
                    ->getStateUsing(function (TenantProductVariant $record) {
                        return $record->variantAttributes->map(function ($pivot) {
                            $attrName = $pivot->attribute?->name ?? '??';
                            $value = $pivot->attributeValue?->value ?? $pivot->custom_value;
                            $unit = $pivot->attribute?->unit ?? '';

                            return "{$attrName}: {$value}{$unit}";
                        })->toArray();
                    })
                    ->limitList(2)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (is_array($state) && count($state) > 2) {
                            return implode(', ', $state);
                        }

                        return null;
                    }),

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
                    ->label('KS')
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
                    ->modalHeading('Pridať variant produktu '.$this->getOwnerRecord()?->name)
                    ->label('Pridať variant'),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::SevenExtraLarge)
                    ->modalHeading(fn ($record) => 'Upraviť variantu '.$record?->name)
                    ->label('Upraviť'),
                DeleteAction::make()
                    ->label('Zmazať'),
                AdjustStockAction::make(),
                HistoryAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
