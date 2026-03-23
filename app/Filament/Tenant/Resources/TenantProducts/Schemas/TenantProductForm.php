<?php

namespace App\Filament\Tenant\Resources\TenantProducts\Schemas;

use App\Filament\Actions\GenerateDescipritonAction;
use App\Filament\Tenant\Resources\GlobalProductRequests\GlobalProductRequestResource;
use App\Models\GlobalProduct;
use App\Models\TenantProduct;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
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
                            ->label('Názov')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                fn (?TenantProduct $record): \Illuminate\Validation\Rules\Unique => Rule::unique('tenant_products', 'slug')
                                    ->where('tenant_id', Filament::getTenant()->id)
                                    ->ignore($record?->id),
                            ]),

                        RichEditor::make('description')
                            ->label('Popis')
                            ->required()
                            ->columnSpanFull()
                            ->hintAction(
                                GenerateDescipritonAction::make()
                                    ->references('description')
                                    ->title('name')
                                    ->context('produkt')
                            ),

                        Toggle::make('is_active')
                            ->label('Aktívny')
                            ->default(true),

                        SpatieMediaLibraryFileUpload::make('media')
                            ->collection('tenant_products')
                            ->multiple()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->label('Obrázky produktu')
                            ->columnSpanFull(),
                    ]),

                Section::make('Prepojenie s katalógom')
                    ->description('Prepojte produkt s globálnym katalógom alebo požiadajte o pridanie nového.')
                    ->schema([
                        Select::make('global_product_id')
                            ->relationship('globalProduct', 'name')
                            ->label('Globálny produkt')
                            ->placeholder('vlastný produkt')
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
                                    ->label('Požiadať o nový produkt')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('info')
                                    ->url(fn (): string => GlobalProductRequestResource::getUrl('create'))
                            ),
                    ]),
            ]);
    }
}
