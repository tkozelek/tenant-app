<?php

namespace App\Filament\Tenant\Resources\GlobalProductRequests\Schemas;

use App\Filament\Actions\GenerateDescipritonAction;
use App\Models\GlobalProductRequest;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GlobalProductRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informácie')
                    ->disabled(fn (?GlobalProductRequest $record): bool => $record !== null && $record->status !== 'pending')
                    ->schema([
                        TextInput::make('suggested_name')
                            ->label('Nazov produktu')
                            ->required()
                            ->maxLength(255),

                        Select::make('suggested_category_id')
                            ->relationship('suggestedCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Kategoria'),

                        RichEditor::make('suggested_description')
                            ->hintAction(
                                GenerateDescipritonAction::make()
                                    ->references('suggested_description')
                                    ->title('suggested_name')
                                    ->context('produkt')
                            )
                            ->label('Popis')
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('images')
                            ->collection('request_images')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->panelLayout('grid')
                            ->visibility('public')
                            ->label('Obrázky produktu')
                            ->columnSpanFull(),
                    ]),

                Section::make('Stav')
                    ->schema([
                        Select::make('status')
                            ->label('Stav')
                            ->options([
                                'pending' => 'Čaká',
                                'approved' => 'Schválená',
                                'rejected' => 'Zamietnutá',
                            ])
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('created_global_product_id')
                            ->relationship('createdGlobalProduct', 'name')
                            ->label('Vytvoreny produkt')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (?GlobalProductRequest $record): bool => $record?->created_global_product_id !== null),

                        Textarea::make('admin_note')
                            ->label('Poznámka od admina')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->visible(fn (?GlobalProductRequest $record): bool => filled($record?->admin_note)),
                    ]),
            ])->disabled(fn (?GlobalProductRequest $record): bool => $record !== null && $record->status !== 'pending');
    }
}
