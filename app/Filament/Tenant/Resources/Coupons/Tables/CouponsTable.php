<?php

namespace App\Filament\Tenant\Resources\Coupons\Tables;

use App\Filament\Exports\CouponExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('discount_type')
                    ->label('Typ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Percento',
                        'fixed' => 'Suma',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('categories.name')
                    ->label('Kategorie')
                    ->badge()
                    ->color('gray')
                    ->limitList(2)
                    ->tooltip(fn ($record) => $record->categories->pluck('name')->join(', ')),

                TextColumn::make('value')
                    ->label('Hodnota')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->discount_type === 'percentage'
                        ? "{$record->value}%"
                        : "{$record->value} €"),

                TextColumn::make('usage_limit')
                    ->label('Pouzitie')
                    ->formatStateUsing(fn ($record) => "{$record->used_count} / ".($record->usage_limit ?? '∞'))
                    ->sortable(['used_count', 'usage_limit']),

                TextColumn::make('expires_at')
                    ->label('Platny do')
                    ->dateTime('d. m. Y H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : null),

                IconColumn::make('is_active')
                    ->label('Aktivny')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Aktivny')
                    ->boolean()
                    ->placeholder('Vsetky'),

                Filter::make('expired')
                    ->label('Expirovane')
                    ->query(fn ($query) => $query->where('expires_at', '<', now()))
                    ->toggle(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(CouponExporter::class)
                    ->modifyQueryUsing(fn ($query) => $query->where('tenant_id', Filament::getTenant()?->id)),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
