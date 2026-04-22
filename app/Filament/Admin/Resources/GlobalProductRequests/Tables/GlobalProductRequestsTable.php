<?php

namespace App\Filament\Admin\Resources\GlobalProductRequests\Tables;

use App\Filament\Admin\Resources\GlobalProductRequests\Tables\actions\ApproveAndCreateAction;
use App\Filament\Admin\Resources\GlobalProductRequests\Tables\actions\RejectAction;
use App\Filament\Exports\GlobalProductRequestExporter;
use App\Models\GlobalProductRequest;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GlobalProductRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('suggested_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('tenant.name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Čaká',
                        'approved' => 'Prijatý',
                        'rejected' => 'Zamietnutý',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->placeholder('Vyberte')
                    ->options([
                        'pending' => 'Čaká',
                        'approved' => 'Prijatý',
                        'rejected' => 'Zamietnutý',
                    ])->default('pending'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(function (GlobalProductRequest $record) {
                        return $record->status === 'approved' ? 'View' : 'Edit';
                    }),
                ApproveAndCreateAction::make(),
                RejectAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(GlobalProductRequestExporter::class)
                        ->authorize('exportAny'),
                ]),
            ]);
    }
}
