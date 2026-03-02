<?php

namespace App\Filament\Resources\GlobalProductRequests\Schemas;

use App\Models\GlobalProductRequest;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
                Section::make('Request Information')
                    ->disabled(fn (?GlobalProductRequest $record) => $record?->status === 'approved')
                    ->schema([
                        Select::make('tenant_id')
                            ->relationship('tenant', 'name'),

                        Select::make('requested_by_user_id')
                            ->relationship('requestedBy', 'email')
                            ->label('Vytvoril'),

                        TextInput::make('suggested_name')
                            ->label("Názov"),

                        RichEditor::make('suggested_description')
                            ->label('Popis')
                            ->columnSpanFull(),

                        Select::make('suggested_category_id')
                            ->relationship('suggestedCategory', 'name')
                            ->label('Kategória'),

                        Select::make('status')
                            ->options([
                                'pending' => 'Čaká',
                                'rejected' => 'Zamietnutý',
                                'approved' => 'Potvrdený'
                            ]),

                        Select::make('created_global_product_id')
                            ->relationship('createdGlobalProduct', 'name')
                            ->label('Výsledny produkt')
                            ->visible(fn (?GlobalProductRequest $record) => $record?->created_global_product_id !== null),
                        ]),
                Section::make('Admin poznámka')
                    ->schema([
                        Textarea::make('admin_note')
                            ->label('Poznámka (admin)')
                            ->columnSpanFull()
                    ])
            ]);
    }
}
