<?php

namespace App\Filament\Tenant\Resources\Bundles\Schemas;

use App\Filament\Actions\GenerateDescipritonAction;
use App\Models\TenantProductVariant;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BundleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Detail')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Názov')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                RichEditor::make('description')
                                    ->hintAction(
                                        GenerateDescipritonAction::make()
                                            ->context('balik (bundle viacerych produktov)')
                                            ->title('name')
                                            ->references('description')
                                            ->extraContext(function (Get $get): string {
                                                $items = $get('items') ?? [];
                                                if (empty($items)) {
                                                    return '';
                                                }

                                                $ids = collect($items)->pluck('tenant_product_variant_id')->filter();
                                                $variants = TenantProductVariant::whereIn('id', $ids)->pluck('name', 'id');

                                                $list = collect($items)
                                                    ->filter(fn ($i) => ! empty($i['tenant_product_variant_id']))
                                                    ->map(fn ($i) => ($variants[$i['tenant_product_variant_id']] ?? 'Neznamy').' (x'.($i['quantity'] ?? 1).')')
                                                    ->join(', ');

                                                return "Balik obsahuje tieto produkty: {$list}.";
                                            })
                                    )
                                    ->columnSpanFull()
                                    ->label('Popis'),

                                Toggle::make('is_active')
                                    ->default(true)
                                    ->required(),

                                TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->nullable()
                                    ->columnSpanFull()
                                    ->placeholder('https://...')
                                    ->helperText('Ak je vyplnené, zobrazí sa tlačidlo Kúpiť na frontende.'),
                            ])->columns(),
                        Section::make('Médiá')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('media')
                                    ->collection('bundles')
                                    ->multiple()
                                    ->reorderable()
                                    ->panelLayout('compact')
                                    ->visibility('public')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Group::make()
                    ->schema([
                        Section::make('Cenotvorba')
                            ->schema([
                                TextInput::make('initial_price')
                                    ->label('Aktuálna cena')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step('0.01'),

                                TextInput::make('initial_original_price')
                                    ->label('Pôvodná cena (pred zľavou)')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step('0.01')
                                    ->helperText('Vyplňte, ak je produkt v zľave.'),

                                DateTimePicker::make('initial_valid_from')
                                    ->label('Platné od')
                                    ->default(now()),

                                DateTimePicker::make('initial_valid_to')
                                    ->label('Platné do')
                                    ->after('initial_valid_from')
                                    ->helperText('Nepovinné. Ak je vyplnené, cena sa automaticky deaktivuje po tomto dátume.'),

                                TextEntry::make('total_price')
                                    ->label('Hodnota poloziek v baliku')
                                    ->state(function (Get $get) {
                                        $items = $get('items') ?? [];
                                        $ids = collect($items)->pluck('tenant_product_variant_id')->filter()->toArray();

                                        if (empty($ids)) {
                                            return '0.00';
                                        }

                                        $prices = TenantProductVariant::whereIn('id', $ids)->get()->pluck('current_price', 'id');

                                        $total = 0;
                                        foreach ($items as $item) {
                                            $id = $item['tenant_product_variant_id'] ?? null;
                                            $qty = floatval($item['quantity'] ?? 0);

                                            if ($id && isset($prices[$id])) {
                                                $total += $prices[$id] * $qty;
                                            }
                                        }

                                        return number_format($total, 2, ',', ' ').' €';
                                    }),
                            ]),

                        Section::make('Produkty')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship()
                                    ->itemLabel(function (array $state): ?string {
                                        $variantId = $state['tenant_product_variant_id'] ?? null;
                                        if (! $variantId) {
                                            return null;
                                        }

                                        return sprintf('%s - %s ks', TenantProductVariant::whereKey($variantId)->value('name'), $state['quantity'] ?? 0);
                                    })
                                    ->schema([
                                        Select::make('tenant_product_variant_id')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Vyberte produkt variantu')
                                            ->live(debounce: 300)
                                            ->options(fn () => TenantProductVariant::whereHas(
                                                'product',
                                                fn ($q) => $q->where('tenant_id', Filament::getTenant()?->id)
                                            )->pluck('name', 'id'))
                                            ->label('Produkt variant'),

                                        TextInput::make('quantity')
                                            ->label('Pocet')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->live(debounce: 300),
                                    ])
                                    ->columns()
                                    ->defaultItems(1)
                                    ->addActionLabel('Pridať variant do balika'),
                            ]),
                    ]),
            ]);
    }
}
