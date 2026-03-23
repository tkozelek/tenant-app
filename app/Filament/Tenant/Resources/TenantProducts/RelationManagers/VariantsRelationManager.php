<?php

namespace App\Filament\Tenant\Resources\TenantProducts\RelationManagers;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\AdjustStockAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\HistoryAction;
use App\Filament\Admin\Resources\TenantProductVariants\Schemas\TenantProductVariantForm;
use App\Filament\Admin\Resources\TenantProductVariants\Tables\TenantProductVariantsTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Varianty';

    public function form(Schema $schema): Schema
    {
        return TenantProductVariantForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        $table = TenantProductVariantsTable::configure($table);

        return $table
            ->recordTitleAttribute('name')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                HistoryAction::make(),
                AdjustStockAction::make()
                    ->after(fn (RelationManager $livewire) => $livewire->dispatch('refresh')),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
