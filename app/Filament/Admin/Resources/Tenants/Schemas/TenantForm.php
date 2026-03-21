<?php

namespace App\Filament\Admin\Resources\Tenants\Schemas;

use App\Filament\Actions\GenerateDescipritonAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class TenantForm
{
    public static function configure($schema)
    {
        return $schema->schema([

            Section::make('Info')
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->prefix(config('app.url')),

                    TextInput::make('short_description')
                        ->label('Tagline (meta)')
                        ->placeholder('A short version of description'),

                    RichEditor::make('description')
                        ->label('Description')
                        ->hintAction(
                            GenerateDescipritonAction::make()
                                ->references('description')
                                ->title('name')
                                ->context('tenant popis'),
                        )
                        ->toolbarButtons([
                            'attachFiles',
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'undo',
                        ])
                        ->columnSpanFull(),
                ]),

            Section::make('Branding')
                ->description('Manage tenant imamges.')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('title_image')
                        ->label('Titulka')
                        ->collection('titles')
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull()
                        ->helperText('Banner over the apge'),

                    SpatieMediaLibraryFileUpload::make('image')
                        ->label('Tenant logo')
                        ->collection('images')
                        ->image()
                        ->alignCenter()
                        ->helperText('Upload a square profile image'),
                ]),

            Section::make('Administration')
                ->schema([
                    Select::make('owner_id')
                        ->label('Owner')
                        ->relationship('owner', 'email')
                        ->default(auth()->user()->id)
                        ->searchable()
                        ->preload()
                        ->required(),

                    Toggle::make('is_public')
                        ->label('Public?')
                        ->inline(false)
                        ->default(true),
                ]),
        ]);
    }
}
