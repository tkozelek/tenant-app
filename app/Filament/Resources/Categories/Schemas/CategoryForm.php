<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(Category::class, 'slug', ignoreRecord: true),

                Select::make('parent_id')
                    ->relationship('parent', 'name', ignoreRecord: true)
                    ->searchable()
                    ->preload()
                    ->label('Nadkategória'),

                Select::make('attributes')
                    ->relationship('attributes', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Atribúty'),
            ]);
    }
}
