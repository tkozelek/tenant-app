<?php

namespace App\Filament\Tenant\Resources\TenantProducts\RelationManagers;

use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\AdjustPriceAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\AdjustStockAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\FlashSaleAction;
use App\Filament\Admin\Resources\TenantProducts\RelationManagers\actions\HistoryAction;
use App\Filament\Admin\Resources\TenantProductVariants\Schemas\TenantProductVariantForm;
use App\Filament\Admin\Resources\TenantProductVariants\Tables\TenantProductVariantsTable;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                AssociateAction::make()
                    ->label('Pripojiť')
                    ->recordSelectSearchColumns(['name', 'sku'])
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query
                        ->whereHas('product', fn (Builder $q) => $q->where('tenant_id', Filament::getTenant()->id))
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                HistoryAction::make(),
                AdjustPriceAction::make(),
                FlashSaleAction::make(),
                AdjustStockAction::make()
                    ->after(fn (RelationManager $livewire) => $livewire->dispatch('refresh')),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
