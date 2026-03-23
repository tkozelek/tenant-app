<?php

namespace App\Filament\Admin\Resources\TenantProducts\Schemas;

use App\Filament\Actions\GenerateDescipritonAction;
use App\Filament\Admin\Resources\GlobalProducts\GlobalProductResource;
use App\Models\GlobalProduct;
use App\Models\TenantProduct;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantProductForm
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
                            ->rules([
                                fn (Get $get, ?TenantProduct $record): \Illuminate\Validation\Rules\Unique => Rule::unique('tenant_products', 'slug')
                                    ->where('tenant_id', $get('tenant_id'))
                                    ->ignore($record?->id),
                            ]),
                        RichEditor::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->hintAction(
                                GenerateDescipritonAction::make()
                                    ->references('description')
                                    ->title('name')
                                    ->context('produkt')
                            ),
                        Toggle::make('is_active')
                            ->label('Aktivny')
                            ->default(true),
                        SpatieMediaLibraryFileUpload::make('media')
                            ->collection('tenant_products')
                            ->multiple()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->label('Obrázky produktu')
                            ->columnSpanFull(),
                    ]),

                Section::make('Prepojenie')
                    ->schema([
                        Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('global_product_id')
                            ->relationship('globalProduct', 'name')
                            ->placeholder('-')
                            ->searchable()
                            ->preload()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, $state) {
                                if ($state) {
                                    $globalProduct = GlobalProduct::find($state);
                                    if ($globalProduct) {
                                        $set('name', $globalProduct->name);
                                        $set('description', $globalProduct->description);
                                    }
                                }
                            })
                            ->suffixAction(
                                Action::make('requestGlobalProduct')
                                    ->label('Vytvoriť nový globálný produkt')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('info')
                                    ->url(fn (): string => GlobalProductResource::getUrl('create'))
                            ),
                    ])->columns(),
            ]);
    }
}
