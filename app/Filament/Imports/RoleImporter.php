<?php

namespace App\Filament\Imports;

use App\Enums\PermissionScope;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class RoleImporter extends Importer
{
    protected static ?string $model = Role::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('description')
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('guard_name')
                ->rules(['nullable', 'string', 'max:255'])
                ->castStateUsing(fn (?string $state): string => $state ?: 'web'),

            ImportColumn::make('tenant_id')
                ->label('Tenant (slug)')
                ->rules(['nullable', 'string'])
                ->castStateUsing(fn (?string $state): ?int => $state
                    ? Tenant::where('slug', $state)->value('id')
                    : null
                ),

            ImportColumn::make('permissions')
                ->label('Permissions (pipe-separated)')
                ->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): Role
    {
        return Role::firstOrNew([
            'name' => $this->data['name'],
            'guard_name' => $this->data['guard_name'] ?? 'web',
        ]);
    }

    protected function afterSave(): void
    {
        if (empty($this->data['permissions'])) {
            return;
        }

        $permissionNames = array_filter(array_map('trim', explode('|', $this->data['permissions'])));

        $query = Permission::whereIn('name', $permissionNames);

        if ($this->options['tenant_scope_only'] ?? false) {
            $query->where('scope', PermissionScope::Tenant);
        }

        $this->record->syncPermissions($query->get());
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your role import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
