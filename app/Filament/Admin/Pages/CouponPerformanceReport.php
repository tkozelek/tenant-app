<?php

namespace App\Filament\Admin\Pages;

use App\Models\Coupon;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouponPerformanceReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.admin.pages.coupon-performance-report';

    protected static ?string $title = 'Vykonnost kuponov';

    protected static ?string $navigationLabel = 'Vykonnost kuponov';

    protected static string|\UnitEnum|null $navigationGroup = 'Reporty';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        return $user?->hasPermissionTo('reports.coupon_performance') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Coupon::query()
                    ->withCount('productVariants as variant_count')
                    ->withCount('categories as category_count')
            )
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('discount_type')
                    ->label('Typ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Percento',
                        'fixed' => 'Suma',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('value')
                    ->label('Zľava')
                    ->sortable()
                    ->state(fn ($record) => $record->discount_type === 'percentage'
                        ? "{$record->value}%"
                        : number_format((float) $record->value, 2).' €'
                    ),

                TextColumn::make('usage')
                    ->label('Vyuzitie')
                    ->sortable(['used_count', 'usage_limit'])
                    ->state(fn ($record) => "{$record->used_count} / ".($record->usage_limit ?? 'inf.'))
                    ->badge()
                    ->color(fn ($record) => $record->usage_limit && $record->used_count >= $record->usage_limit
                        ? 'danger'
                        : 'success'
                    ),

                TextColumn::make('usage_rate')
                    ->label('Vyuzitie (%)')
                    ->state(fn ($record) => $record->usage_limit
                        ? number_format(($record->used_count / $record->usage_limit) * 100, 1).'%'
                        : '-'
                    )
                    ->color(fn ($record) => $record->usage_limit && $record->used_count / $record->usage_limit > 0.8
                        ? 'warning'
                        : null
                    ),

                TextColumn::make('estimated_discount')
                    ->label('Odhadovana zlava')
                    ->state(fn ($record) => $record->discount_type === 'fixed'
                        ? number_format((float) $record->value * $record->used_count, 2).' €'
                        : "{$record->value}% * {$record->used_count}x"
                    )
                    ->color('warning'),

                TextColumn::make('variant_count')
                    ->label('Varianty')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('category_count')
                    ->label('Kategorie')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('expires_at')
                    ->label('Platny do')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : null),

                IconColumn::make('is_active')
                    ->label('Aktivny')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tenant')
                    ->label('Tenant')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label('Aktivny')
                    ->placeholder('Vsetky'),
            ])
            ->defaultSort('used_count', 'desc');
    }
}
