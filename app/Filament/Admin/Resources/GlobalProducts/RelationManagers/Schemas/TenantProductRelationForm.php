<?php

namespace App\Filament\Admin\Resources\GlobalProducts\RelationManagers\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;

class TenantProductRelationForm
{
    public static function configure(Schema $schema, RelationManager $livewire): Schema
    {
        $ownerRecord = $livewire->getOwnerRecord();

        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('name')
                    ->label('Názov')
                    ->required()
                    ->maxLength(255)
                    ->default($ownerRecord?->name),

                Toggle::make('is_active')
                    ->label('Aktívny')
                    ->default(true),

                RichEditor::make('description')
                    ->label('Popis')
                    ->columnSpanFull()
                    ->default($ownerRecord?->description),
            ]);
    }
}
