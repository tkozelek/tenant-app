<?php

namespace App\Filament\Admin\Resources\TenantProducts\RelationManagers;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\AdjustPriceAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\AdjustStockAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\HistoryAction;
use App\Filament\Admin\Resources\TenantProductVariants\Schemas\TenantProductVariantForm;
use App\Filament\Admin\Resources\TenantProductVariants\Tables\TenantProductVariantsTable;
use App\Filament\Admin\Resources\TenantProductVariants\TenantProductVariantResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
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
                    ->url(fn ($record) => TenantProductVariantResource::getUrl('edit', ['record' => $record]))
                    ->label('Upraviť'),
                DeleteAction::make()
                    ->label('Zmazať'),
                AdjustPriceAction::make(),
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
