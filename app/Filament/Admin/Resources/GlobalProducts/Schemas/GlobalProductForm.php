<?php

namespace App\Filament\Admin\Resources\GlobalProducts\Schemas;

use App\Filament\Actions\GenerateDescipritonAction;
use App\Filament\Components\ProductAttributesSection;
use App\Models\GlobalProduct;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class GlobalProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(GlobalProduct::class, 'slug', ignoreRecord: true),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->label('Kategória'),

                        Toggle::make('is_active')
                            ->label('Aktívny')
                            ->default(true)
                            ->inline(false),

                        RichEditor::make('description')
                            ->columnSpanFull()
                            ->hintAction(
                                GenerateDescipritonAction::make()
                                    ->references('description')
                                    ->title('name')
                                    ->context('produkt')
                            ),
                    ]),

                ProductAttributesSection::make(
                    relationship: 'globalProductAttributes',
                    categoryIdResolver: fn (Component $livewire): ?int => $livewire->data['category_id'] ?? null,
                ),

                Section::make('Médiá')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->collection('global_products')
                            ->multiple()
                            ->reorderable()
                            ->panelLayout('compact')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
