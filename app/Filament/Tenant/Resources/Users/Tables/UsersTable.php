<?php

namespace App\Filament\Tenant\Resources\Users\Tables;

use App\Models\Role;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label('Meno')
                    ->searchable(),

                TextColumn::make('last_name')
                    ->label('Priezvisko')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('tenant_role')
                    ->label('Rola')
                    ->state(function (Model $record): string {
                        $tenantId = Filament::getTenant()?->id;

                        if ($record->id === Filament::getTenant()?->owner_id) {
                            return 'Owner';
                        }

                        return $record->roles()
                            ->wherePivot('tenant_id', $tenantId)
                            ->first()
                            ?->name ?? '-';
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Vytvorený')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rola')
                    ->options(fn (): array => Role::query()
                        ->where(fn (Builder $query) => $query
                            ->whereDoesntHave('permissions', fn ($q) => $q->where('name', 'platform.access'))
                            ->orWhere('tenant_id', Filament::getTenant()?->id)
                        )
                        ->pluck('name', 'id')
                        ->toArray()
                    )
                    ->query(function (Builder $query, array $data): void {
                        if (filled($data['value'])) {
                            $query->whereHas('roles', fn (Builder $q) => $q
                                ->where('roles.id', $data['value'])
                                ->wherePivot('tenant_id', Filament::getTenant()?->id)
                            );
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->label('Odstrániť')
                    ->action(function (User $record): void {
                        $tenantId = Filament::getTenant()->id;

                        $record->roles()
                            ->wherePivot('tenant_id', $tenantId)
                            ->detach();
                    })
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Odstrániť')
                        ->action(function ($records): void {
                            $tenantId = Filament::getTenant()->id;

                            foreach ($records as $user) {
                                $user->roles()
                                    ->wherePivot('tenant_id', $tenantId)
                                    ->detach();
                            }
                        }),
                ]),
            ]);
    }
}
