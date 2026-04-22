<?php

namespace App\Filament\Admin\Resources\Coupons\Schemas;

use App\Filament\Actions\GenerateDescipritonAction;
use App\Models\Category;
use App\Models\TenantProductVariant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Vseobecne informacie')
                            ->schema([
                                Select::make('tenant_id')
                                    ->label('Tenant')
                                    ->relationship('tenant', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('tenantProductVariants', [])),

                                TextInput::make('code')
                                    ->label('Kod')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule) {
                                        return $rule->where('tenant_id', request()->input('tenant_id'));
                                    })
                                    ->extraInputAttributes(['onChange' => 'this.value = this.value.toUpperCase()']),

                                RichEditor::make('description')
                                    ->label('Popis')
                                    ->nullable()
                                    ->columnSpanFull()
                                    ->hintAction(
                                        GenerateDescipritonAction::make()
                                            ->context('kupon')
                                            ->title('code')
                                            ->references('description')
                                            ->extraContext(function (Get $get): string {
                                                $parts = [];

                                                $categoryIds = $get('categories') ?? [];
                                                if (! empty($categoryIds)) {
                                                    $names = Category::whereIn('id', $categoryIds)->pluck('name')->join(', ');
                                                    $parts[] = "Kupon je aplikovatelny na kategorie: {$names}.";
                                                }

                                                $variantIds = $get('tenantProductVariants') ?? [];
                                                if (! empty($variantIds)) {
                                                    $names = TenantProductVariant::whereIn('id', $variantIds)->pluck('name')->join(', ');
                                                    $parts[] = "Kupon je aplikovatelny na varianty produktov: {$names}.";
                                                }

                                                return implode(' ', $parts);
                                            })
                                    ),
                            ]),
                        Section::make('Detail')
                            ->schema([
                                Select::make('discount_type')
                                    ->label('Typ zlavy')
                                    ->options([
                                        'fixed' => 'Pevna zlava',
                                        'percentage' => '%',
                                    ])
                                    ->required()
                                    ->default('percentage'),
                                TextInput::make('value')
                                    ->label('hodnota')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0),
                            ])->columns(),

                        Section::make('Aplikovatelne na')
                            ->schema([
                                Select::make('categories')
                                    ->label('Kategorie')
                                    ->relationship('categories', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                                Select::make('tenantProductVariants')
                                    ->label('Varianty produktov')
                                    ->relationship(
                                        'productVariants',
                                        'name',
                                        fn ($query, Get $get) => $query->whereHas(
                                            'product',
                                            fn ($q) => $q->where('tenant_id', $get('tenant_id'))
                                        )
                                    )
                                    ->multiple()
                                    ->searchable(),
                            ])->columns(),
                    ]),

                Group::make()
                    ->schema([
                        Section::make('Stav a pravidla')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Aktivny')
                                    ->default(true)
                                    ->required(),

                                TextInput::make('min_order_amount')
                                    ->label('Min hodnota')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('€')
                                    ->nullable(),

                                TextInput::make('usage_limit')
                                    ->label('Limit pouzitii')
                                    ->numeric()
                                    ->minValue(1)
                                    ->nullable(),

                                TextInput::make('used_count')
                                    ->label('Počet pouzitii')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                        Section::make('Platnost')
                            ->schema([
                                DateTimePicker::make('starts_at')
                                    ->label('Platny od')
                                    ->nullable(),
                                DateTimePicker::make('expires_at')
                                    ->label('Platny do')
                                    ->afterOrEqual('starts_at')
                                    ->nullable(),
                            ]),
                    ]),
            ])->columns();
    }
}
