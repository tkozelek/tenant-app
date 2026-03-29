<?php

namespace App\Filament\Tenant\Resources\ActivityLogs;

use App\Filament\Tenant\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Tenant\Resources\ActivityLogs\Pages\ViewActivityLog;
use App\Filament\Tenant\Resources\ActivityLogs\Schemas\ActivityLogInfolist;
use App\Filament\Tenant\Resources\ActivityLogs\Tables\ActivityLogsTable;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Activity;
use App\Policies\ActivityLogPolicy;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $policy = ActivityLogPolicy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 99;

    protected static bool $isScopedToTenant = false;


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Filament::getTenant()->id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
            'view' => ViewActivityLog::route('/{record}'),
        ];
    }
}
