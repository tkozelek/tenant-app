<?php

namespace App\Filament\Admin\Resources\ActivityLogs\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Activitylog\Models\Activity;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event detaily')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('event')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('log_name')
                            ->label('Type')
                            ->badge()
                            ->color('gray')
                            ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),

                        TextEntry::make('created_at')
                            ->label('When')
                            ->dateTime(),

                        TextEntry::make('subject_type')
                            ->label('Model')
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),

                        TextEntry::make('subject_id')
                            ->label('Model ID'),

                        TextEntry::make('causer.full_name')
                            ->label('By')
                            ->placeholder('System'),
                    ]),

                Section::make('New Values')
                    ->visible(fn (Activity $record): bool => filled($record->properties->get('attributes')))
                    ->schema([
                        KeyValueEntry::make('properties.attributes')
                            ->label('')
                            ->columnSpanFull(),
                    ]),

                Section::make('Previous Values')
                    ->visible(fn (Activity $record): bool => filled($record->properties->get('old')))
                    ->schema([
                        KeyValueEntry::make('properties.old')
                            ->label('')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
