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

            Section::make('Informácie')
                ->schema([
                    TextInput::make('name')
                        ->label('Názov')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->prefix(config('app.url').'obchod/'),

                    TextInput::make('website_url')
                        ->label('URL e-shopu')
                        ->url()
                        ->maxLength(2048)
                        ->placeholder('https://dr-max.com/')
                        ->helperText('Základná URL adresa e-shopu tenantu. Bude sa predvypĺňať pri produktových odkazoch.'),

                    TextInput::make('short_description')
                        ->label('Tagline (meta)')
                        ->placeholder('Krátky popis'),

                    RichEditor::make('description')
                        ->label('Popis')
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
                ->description('Obrázky tenanta.')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('title_image')
                        ->label('Titulka')
                        ->collection('titles')
                        ->image()
                        ->imageEditor()
                        ->visibility('public')
                        ->columnSpanFull()
                        ->helperText('Banner nad stránkou.'),

                    SpatieMediaLibraryFileUpload::make('image')
                        ->label('Tenant logo')
                        ->collection('images')
                        ->image()
                        ->alignCenter()
                        ->visibility('public')
                        ->helperText('Štvorcový profilový obrázok.'),
                ]),

            Section::make('Administrácia')
                ->schema([
                    Select::make('owner_id')
                        ->label('Vlastník')
                        ->relationship('owner', 'email')
                        ->default(auth()->user()->id)
                        ->searchable()
                        ->preload()
                        ->required(),

                    Toggle::make('is_public')
                        ->label('Verejný?')
                        ->inline(false)
                        ->default(true),
                ]),
        ]);
    }
}
