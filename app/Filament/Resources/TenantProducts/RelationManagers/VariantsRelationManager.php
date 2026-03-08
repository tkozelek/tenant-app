<?php

namespace App\Filament\Resources\TenantProducts\RelationManagers;

use App\Filament\Resources\TenantProducts\RelationManagers\actions\AdjustStockAction;
use App\Filament\Resources\TenantProducts\RelationManagers\actions\HistoryAction;
use App\Filament\Resources\TenantProducts\RelationManagers\components\VariantAttributesSection;
use App\Filament\Resources\TenantProductVariants\Schemas\TenantProductVariantForm;
use App\Filament\Resources\TenantProductVariants\Tables\TenantProductVariantsTable;
use App\Models\TenantProductVariant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $recordTitleAttribute = 'sku';

    public function form(Schema $schema): Schema
    {
        return TenantProductVariantForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return TenantProductVariantsTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Pridať variant produktu '.$this->getOwnerRecord()?->name)
                    ->label('Pridať variant'),
                ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::SevenExtraLarge)
                    ->modalHeading(fn ($record) => 'Upraviť variantu '.$record?->name)
                    ->label('Upraviť'),
                DeleteAction::make()
                    ->label('Zmazať'),
                AdjustStockAction::make(),
                HistoryAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
