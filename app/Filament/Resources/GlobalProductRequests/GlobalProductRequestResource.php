<?php

namespace App\Filament\Resources\GlobalProductRequests;

use App\Filament\Resources\GlobalProductRequests\Pages\CreateGlobalProductRequest;
use App\Filament\Resources\GlobalProductRequests\Pages\EditGlobalProductRequest;
use App\Filament\Resources\GlobalProductRequests\Pages\ListGlobalProductRequests;
use App\Filament\Resources\GlobalProductRequests\Schemas\GlobalProductRequestForm;
use App\Filament\Resources\GlobalProductRequests\Tables\GlobalProductRequestsTable;
use App\Models\GlobalProductRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class GlobalProductRequestResource extends Resource
{
    protected static ?string $model = GlobalProductRequest::class;

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?string $recordTitleAttribute = 'suggested_name';

    public static function getNavigationBadge(): ?string
    {
        return Cache::remember('global_product_requests_count', 5*60, function () {
            return GlobalProductRequest::where('status', 'pending')->count();
        });
    }

    public static function form(Schema $schema): Schema
    {
        return GlobalProductRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlobalProductRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGlobalProductRequests::route('/'),
            'edit' => EditGlobalProductRequest::route('/{record}/edit'),
        ];
    }
}
