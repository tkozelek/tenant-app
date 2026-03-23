<?php

namespace App\Filament\Admin\Resources\GlobalProductRequests\Tables\actions;

use App\Filament\Actions\GenerateDescipritonAction;
use App\Models\Category;
use App\Models\GlobalProduct;
use App\Models\GlobalProductRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class ApproveAndCreateAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'approve_and_create';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Prijať')
            ->color('success')
            ->icon('heroicon-o-check-badge')
            ->visible(fn (GlobalProductRequest $record) => $record->status === 'pending')
            ->schema([
                TextInput::make('name')
                    ->default(fn (GlobalProductRequest $record) => $record->suggested_name)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->default(fn (GlobalProductRequest $record) => Str::slug($record->suggested_name))
                    ->rule('unique:global_products,slug'),

                Select::make('category_id')
                    ->options(Category::pluck('name', 'id'))
                    ->default(fn (GlobalProductRequest $record) => $record->suggested_category_id)
                    ->required()
                    ->label('Kategória'),

                RichEditor::make('description')
                    ->label('Popis produktu')
                    ->columnSpanFull()
                    ->default(fn (GlobalProductRequest $record) => $record->suggested_description ?? null)
                    ->hintAction(
                        GenerateDescipritonAction::make()
                            ->context('globalny produkt')
                            ->title('name')
                            ->references('description')
                    ),

                SpatieMediaLibraryFileUpload::make('request_images')
                    ->collection('request_images')
                    ->multiple()
                    ->reorderable()
                    ->image()
                    ->panelLayout('grid')
                    ->visibility('public')
                    ->label('Obrázky zo žiadosti')
                    ->helperText('Obrázky nahrané tenantom. Budú skopírované ku globálnemu produktu.')
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, GlobalProductRequest $record) {
                unset($data['request_images']);

                $data['is_active'] = true;
                $globalProduct = GlobalProduct::create($data);

                $record->getMedia('request_images')->each(
                    fn ($media) => $media->copy($globalProduct, 'global_products')
                );

                $record->update([
                    'status' => 'approved',
                    'created_global_product_id' => $globalProduct->id,
                ]);

                $tenantUsers = $record->tenant->allUsers()->get();

                Notification::make()
                    ->title('Žiadosť o produkt schválená')
                    ->body("Váš produkt \"{$record->suggested_name}\" bol schválený a pridaný do globálneho katalógu.")
                    ->success()
                    ->icon('heroicon-o-check-badge')
                    ->sendToDatabase($tenantUsers);

                Notification::make()->title('Globalny produkt vytvoreny, request upraveny.')->success()->send();
            });
    }
}
