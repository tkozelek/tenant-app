<?php

namespace App\Filament\Tenant\Resources\XmlFeeds\Schemas;

use App\Enums\XmlFeedPortal;
use App\Models\TenantProductVariant;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class XmlFeedForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Vseobecne informacie')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nazov')
                            ->required()
                            ->maxLength(255),

                        Select::make('portal')
                            ->label('Portal')
                            ->options(XmlFeedPortal::options())
                            ->required()
                            ->default(XmlFeedPortal::Heureka->value),
                    ])->columns(),

                Section::make('Nastavenia')
                    ->schema([
                        TextInput::make('currency')
                            ->label('Mena')
                            ->required()
                            ->default('EUR')
                            ->maxLength(3),

                        Toggle::make('is_active')
                            ->label('Aktivny')
                            ->default(true)
                            ->required(),

                        Toggle::make('include_out_of_stock')
                            ->label('Zahrnout vypredane varianty')
                            ->default(false)
                            ->required(),
                    ])->columns(),

                Section::make('Filtrovanie')
                    ->description('Ak nie su vybrane ziadne kategorie ani varianty, feed zahrnie vsetky aktivne produkty.')
                    ->schema([
                        Select::make('categories')
                            ->label('Kategorie')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Select::make('variants')
                            ->label('Specificke varianty')
                            ->multiple()
                            ->searchable()
                            ->relationship(
                                name: 'variants',
                                modifyQueryUsing: fn (Builder $query) => $query->with('product')->whereHas(
                                    'product',
                                    fn ($q) => $q->where('tenant_id', Filament::getTenant()?->id)
                                )
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} ({$record->sku})")
                            ->preload(),
                    ])->columns(),
            ]);
    }
}
