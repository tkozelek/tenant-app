<?php

namespace App\Filament\Tenant\Resources\TenantProductVariants\Tables;

use App\Filament\Admin\Resources\TenantProductVariants\Tables\TenantProductVariantsTable as AdminTenantProductVariantsTable;
use App\Filament\Exports\TenantProductVariantExporter;
use App\Filament\Imports\TenantProductVariantImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Facades\Filament;
use Filament\Tables\Table;

class TenantProductVariantsTable
{
    public static function configure(Table $table): Table
    {
        $table = AdminTenantProductVariantsTable::configure($table);

        return $table->toolbarActions([
            ExportAction::make()
                ->exporter(TenantProductVariantExporter::class)
                ->modifyQueryUsing(fn ($query) => $query->whereHas(
                    'product',
                    fn ($q) => $q->where('tenant_id', Filament::getTenant()?->id)
                )),
            ImportAction::make()
                ->importer(TenantProductVariantImporter::class)
                ->options(fn () => ['tenant_id' => Filament::getTenant()?->id]),
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}
