<?php

namespace App\Filament\Admin\Resources\XmlFeeds\Tables;

use App\Enums\XmlFeedPortal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class XmlFeedsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount(['categories', 'variants']))
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nazov')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('portal')
                    ->label('Portal')
                    ->badge()
                    ->formatStateUsing(fn (XmlFeedPortal $state): string => $state->label())
                    ->color('info'),

                TextColumn::make('currency')
                    ->label('Mena')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('categories_count')
                    ->label('Kategorie')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('variants_count')
                    ->label('Varianty')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktivny')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('feed_url')
                    ->label('URL feedu')
                    ->state(fn ($record): string => route('xml-feed.show', [
                        'tenant' => $record->tenant,
                        'token' => $record->token,
                    ]))
                    ->copyable()
                    ->copyMessage('URL skopírovane')
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Vytvorene')
                    ->dateTime('d. m. Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Aktivny')
                    ->boolean()
                    ->placeholder('Vsetky'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
